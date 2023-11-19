<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Hash;
use phpseclib3\Crypt\Random;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PublicKey;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $keyType = 'string';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'username',
        'password',
        'nama',
        'email',
        'tanggal_lahir',
        'alamat',
        'nomor_telepon',
        'enkripsi_digunakan',
        'iv',
        'key_public',
        'key_private',
        'key_enkripsi',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public static function createUser(String $username, String $password) : User
    {
        $hasher = new Hash('sha256');
        $appKey = base64_decode(substr(getenv('APP_KEY'), 7)); // Menghapus 'base64:' dari awal string
        $encryptor = new AES('cbc');
        $encryptor->setKey($appKey);
        $encryptor->setIV(substr($hasher->hash($appKey), 0, 16));

        $privateKey = RSA::createKey();
        $publicKey = $privateKey->getPublicKey();
        $salt = Random::string(24);
        $symmetricKey = $hasher->hash("{$password}.{$salt}");

        $secureSymmetricKey = $encryptor->encrypt($symmetricKey);
        $securePrivateKey = $encryptor->encrypt($privateKey->toString('PKCS8'));
        $securePublicKey = $encryptor->encrypt($publicKey->toString('PKCS8'));

        $userBaru = new User([
            'id' => Str::uuid(),
            'username' => $username,
            'password' => $password,
            'key_enkripsi' => bin2hex($secureSymmetricKey),
            'key_public' => bin2hex($securePrivateKey),
            'key_private' => bin2hex($securePublicKey),
        ]);

        return $userBaru;
    }

    public function setProfile(
        String $enkripsiDigunakan, String $iv, String $nama, String $email,
        String $tanggalLahir, String $alamat, String $nomorTelepon
    ) {
        $this->enkripsi_digunakan = $enkripsiDigunakan;
        $this->iv = bin2hex($iv);
        $this->nama = $nama;
        $this->email = $email;
        $this->tanggal_lahir = $tanggalLahir;
        $this->alamat = $alamat;
        $this->nomor_telepon = $nomorTelepon;
    }

    public function getKeyEnkripsi() : String
    {
        $appKey = base64_decode(substr(getenv('APP_KEY'), 7)); // Menghapus 'base64:' dari awal string
        $hasher = new Hash('sha256');
        $encryptor = new AES('cbc');
        $encryptor->setKey($appKey);
        $encryptor->setIV(substr($hasher->hash($appKey), 0, 16));

        return $encryptor->decrypt(hex2bin($this->key_enkripsi));
    }

    public function getPublicKey() : String
    {
        $appKey = base64_decode(substr(getenv('APP_KEY'), 7)); // Menghapus 'base64:' dari awal string
        $hasher = new Hash('sha256');
        $encryptor = new AES('cbc');
        $encryptor->setKey($appKey);
        $encryptor->setIV(substr($hasher->hash($appKey), 0, 16));

        return $encryptor->decrypt(hex2bin($this->key_public));
    }

    public function getPublicEncryptor() : PublicKey
    {
        $publicKey = $this->getPublicKey();
        $encryptor = RSA::load($publicKey);

        if (!($encryptor instanceof PublicKey)) {
            throw new Exception('Error public key...');
        }
        return $encryptor;
    }

    public function kirimKeyEnkripsiPada(User $tujuan) : String
    {
        $keySendiri = $this->key_enkripsi;
        $encryptor = RSA::load($tujuan->getPublicKey());

        if (!($encryptor instanceof PublicKey)) {
            throw new Exception('Error key tidak sesuai');
        }

        $encryptedKeySendiri = bin2hex($encryptor->encrypt($keySendiri));

        return $encryptedKeySendiri;
    }

    public function getIV() : String
    {
        return hex2bin($this->iv);
    }

    public function file_user() : HasMany
    {
        return $this->hasMany('App\Models\FileUser', 'user_id', 'id');
    }

    public function informasi_user() : HasMany
    {
        return $this->hasMany('App\Models\InformasiUser', 'user_id', 'id');
    }
}
