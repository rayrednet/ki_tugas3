<?php

namespace App\Models;

use App\Helper\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use phpseclib3\Crypt\Random;

class InformasiModel extends Model
{
    use HasFactory;

    public $table = 'informasi';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'nama_informasi',
        'isi_informasi',
        'enkripsi_digunakan',
        'iv',
    ];

    public static function createInformasi(string $key, string $nama_informasi, string $isi_informasi, string $enkripsiDigunakan) : InformasiModel
    {
        $iv = Random::string(16);
        $encryptor = new Encryptor($enkripsiDigunakan, $key, $iv);

        $informasiModel = new InformasiModel();
        $informasiModel->id = Str::uuid();
        $informasiModel->nama_informasi = bin2hex($encryptor->encrypt($nama_informasi));
        $informasiModel->isi_informasi = bin2hex($encryptor->encrypt($isi_informasi));
        $informasiModel->enkripsi_digunakan = $enkripsiDigunakan;
        $informasiModel->iv = bin2hex($iv);

        return $informasiModel;
    }

    public function editInformasi(string $key, string $nama_informasi, string $isi_informasi, string $enkripsiDigunakan) {
        $iv = Random::string(16);
        $encryptor = new Encryptor($enkripsiDigunakan, $key, $iv);

        $this->nama_informasi = bin2hex($encryptor->encrypt($nama_informasi));
        $this->isi_informasi = bin2hex($encryptor->encrypt($isi_informasi));
        $this->enkripsi_digunakan = $enkripsiDigunakan;
        $this->iv = bin2hex($iv);
    }

    public function decryptInformasi() : array {
        $encryptor = new Encryptor($this->enkripsi_digunakan, $this->key->getKeyEnkripsi(), $this->iv());

        $id = $this->id;
        $namaInformasi = $encryptor->decrypt(hex2bin($this->nama_informasi));
        $isiInformasi = $encryptor->decrypt(hex2bin($this->isi_informasi));
        $enkripsiDigunakan = $this->enkripsi_digunakan;

        return [
            'id' => $id,
            'nama_informasi' => $namaInformasi,
            'isi_informasi' => $isiInformasi,
            'enkripsi_digunakan' => $enkripsiDigunakan,
        ];
    }

    public function iv() : string {
        return hex2bin($this->iv);
    }

    public function key() : BelongsTo {
        return $this->belongsTo('App\Models\KeyModel', 'id', 'id')->where('data', '=', $this::class);
    }
}
