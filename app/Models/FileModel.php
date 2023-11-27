<?php

namespace App\Models;

use App\Helper\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use phpseclib3\Crypt\Random;

class FileModel extends Model
{
    use HasFactory;

    public $table = 'file';
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
        'nama_file',
        'nama_file_fisik',
        'enkripsi_digunakan',
        'iv',
    ];

    public static function createFile(string $key, string $namaFile, string $namaFileFisik, string $enkripsiDigunakan) : FileModel
    {
        $iv = Random::string(16);
        $encryptor = new Encryptor($enkripsiDigunakan, $key, $iv);

        $fileModel = new FileModel();
        $fileModel->id = Str::uuid();
        $fileModel->nama_file = bin2hex($encryptor->encrypt($namaFile));
        $fileModel->nama_file_fisik = $namaFileFisik;
        $fileModel->enkripsi_digunakan = $enkripsiDigunakan;
        $fileModel->iv = bin2hex($iv);

        return $fileModel;
    }

    public function decryptFile() : array {
        $encryptor = new Encryptor($this->enkripsi_digunakan, $this->key->getKeyEnkripsi(), $this->iv());

        $id = $this->id;
        $namaFile = $encryptor->decrypt(hex2bin($this->nama_file));
        $namaFileFisik = $this->nama_file_fisik;
        $enkripsiDigunakan = $this->enkripsi_digunakan;

        return [
            'id' => $id,
            'nama_file' => $namaFile,
            'nama_file_fisik' => $namaFileFisik,
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
