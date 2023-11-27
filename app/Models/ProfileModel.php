<?php

namespace App\Models;

use App\Helper\Encryptor;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use phpseclib3\Crypt\Random;

class ProfileModel extends Model
{
    use HasFactory;

    public $table = 'profile';
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
        'nama',
        'email',
        'tanggal_lahir',
        'alamat',
        'nomor_telepon',
        'enkripsi_digunakan',
        'iv',
    ];

    public static function createProfile(string $key, string $nama, string $email, string $tanggalLahir,
        string $alamat, string $nomorTelepon, string $enkripsiDigunakan
    ) : ProfileModel {
        $iv = Random::string(16);
        $encryptor = new Encryptor($enkripsiDigunakan, $key, $iv);

        $profileModel = new ProfileModel();
        $profileModel->id = Str::uuid();
        $profileModel->nama = bin2hex($encryptor->encrypt($nama));
        $profileModel->email = bin2hex($encryptor->encrypt($email));
        $profileModel->tanggal_lahir = bin2hex($encryptor->encrypt($tanggalLahir));
        $profileModel->alamat = bin2hex($encryptor->encrypt($alamat));
        $profileModel->nomor_telepon = bin2hex($encryptor->encrypt($nomorTelepon));
        $profileModel->enkripsi_digunakan = $enkripsiDigunakan;
        $profileModel->iv = bin2hex($iv);

        return $profileModel;
    }


    public function editProfile(string $key, string $nama, string $email, string $tanggalLahir,
        string $alamat, string $nomorTelepon, string $enkripsiDigunakan
    ) {
        $iv = Random::string(16);
        $encryptor = new Encryptor($enkripsiDigunakan, $key, $iv);

        $this->nama = bin2hex($encryptor->encrypt($nama));
        $this->email = bin2hex($encryptor->encrypt($email));
        $this->tanggal_lahir = bin2hex($encryptor->encrypt($tanggalLahir));
        $this->alamat = bin2hex($encryptor->encrypt($alamat));
        $this->nomor_telepon = bin2hex($encryptor->encrypt($nomorTelepon));
        $this->enkripsi_digunakan = $enkripsiDigunakan;
        $this->iv = bin2hex($iv);
    }

    public function decryptProfile() : array {
        $encryptor = new Encryptor($this->enkripsi_digunakan, $this->key->getKeyEnkripsi(), $this->iv());

        $id = $this->id;
        $nama = $encryptor->decrypt(hex2bin($this->nama));
        $email = $encryptor->decrypt(hex2bin($this->email));
        $tanggalLahir = $encryptor->decrypt(hex2bin($this->tanggal_lahir));
        $alamat = $encryptor->decrypt(hex2bin($this->alamat));
        $nomorTelepon = $encryptor->decrypt(hex2bin($this->nomor_telepon));
        $enkripsiDigunakan = $this->enkripsi_digunakan;

        return [
            'id' => $id,
            'nama' => $nama,
            'email' => $email,
            'tanggal_lahir' => $tanggalLahir,
            'alamat' => $alamat,
            'nomor_telepon' => $nomorTelepon,
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
