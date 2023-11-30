<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Hash;
use phpseclib3\Crypt\Random;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PrivateKey;
use phpseclib3\Crypt\RSA\PublicKey;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
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
        'username',
        'password',
        'key_public',
        'key_private',
        'key_enkripsi',
        'digital_signature_public',
        'digital_signature_private',
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

    public static function createUser(string $username, string $password) : User
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
        $securePublicKey = $encryptor->encrypt($publicKey->tostring('PKCS8'));
        $securePrivateKey = $encryptor->encrypt($privateKey->tostring('PKCS8'));

        $userBaru = new User([
            'id' => Str::uuid(),
            'username' => $username,
            'password' => $password,
            'key_enkripsi' => bin2hex($secureSymmetricKey),
            'key_public' => bin2hex($securePublicKey),
            'key_private' => bin2hex($securePrivateKey),
        ]);

        return $userBaru;
    }

    public function setProfile(
        string $enkripsiDigunakan, string $nama, string $email,
        string $tanggalLahir, string $alamat, string $nomorTelepon
    ) {
        $keyModel = KeyModel::query()->where([
            ['key', '=', $this->key_enkripsi],
            ['data', '=', ProfileModel::class]
        ])->first();

        if ($keyModel === null) {
            $profileModel = ProfileModel::createProfile($this->getKeyEnkripsi(), $nama, $email, $tanggalLahir, $alamat, $nomorTelepon, $enkripsiDigunakan);
            $profileModel->save();

            $keyModel = KeyModel::createKeyModel($this->key_enkripsi, ProfileModel::class, $profileModel->id);
            $keyModel->save();
        }
        else {
            /**
             * @var ProfileModel
             */
            $profileModel = $keyModel->profile();
            $profileModel->editProfile($this->getKeyEnkripsi(), $nama, $email, $tanggalLahir, $alamat, $nomorTelepon, $enkripsiDigunakan);
            $profileModel->save();
        }
    }

    public function profile() : HasOneThrough {
        return $this->hasOneThrough('App\Models\ProfileModel', 'App\Models\KeyModel', 'key', 'id', 'key_enkripsi', 'id')->where('data', '=', ProfileModel::class)->with('key');
    }

    public function informasi_user() : HasManyThrough {
        return $this->hasManyThrough('App\Models\InformasiModel', 'App\Models\KeyModel', 'key', 'id', 'key_enkripsi', 'id')
            ->where('key.data', '=', InformasiModel::class)->with('key')
            ->select('informasi.id', 'nama_informasi', 'isi_informasi', 'enkripsi_digunakan', 'iv');
    }

    public function file_user() : HasManyThrough {
        return $this->hasManyThrough('App\Models\FileModel', 'App\Models\KeyModel', 'key', 'id', 'key_enkripsi', 'id')
            ->where('key.data', '=', FileModel::class)->with('key')
            ->select('file.id', 'nama_file', 'nama_file_fisik', 'enkripsi_digunakan', 'iv');
    }

    public function getKeyEnkripsi() : string
    {
        $appKey = base64_decode(substr(getenv('APP_KEY'), 7)); // Menghapus 'base64:' dari awal string
        $hasher = new Hash('sha256');
        $encryptor = new AES('cbc');
        $encryptor->setKey($appKey);
        $encryptor->setIV(substr($hasher->hash($appKey), 0, 16));

        return $encryptor->decrypt(hex2bin($this->key_enkripsi));
    }

    public function getPublicKey() : string
    {
        $appKey = base64_decode(substr(getenv('APP_KEY'), 7)); // Menghapus 'base64:' dari awal string
        $hasher = new Hash('sha256');
        $encryptor = new AES('cbc');
        $encryptor->setKey($appKey);
        $encryptor->setIV(substr($hasher->hash($appKey), 0, 16));

        return $encryptor->decrypt(hex2bin($this->key_public));
    }

    public function getPrivateKey() : string
    {
        $appKey = base64_decode(substr(getenv('APP_KEY'), 7)); // Menghapus 'base64:' dari awal string
        $hasher = new Hash('sha256');
        $encryptor = new AES('cbc');
        $encryptor->setKey($appKey);
        $encryptor->setIV(substr($hasher->hash($appKey), 0, 16));

        return $encryptor->decrypt(hex2bin($this->key_private));
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

    public function getPrivateEncryptor() : PrivateKey
    {
        $publicKey = $this->getPrivateKey();
        $encryptor = RSA::load($publicKey);
        if (!($encryptor instanceof PrivateKey)) {
            throw new Exception('Error private key...');
        }
        return $encryptor;
    }

    public function checkNoKeySignature() : bool
    {
        if ($this->digital_signature_private === null) {
            return true;
        }
        return false;
    }

    private function setDigitalSignature(PrivateKey $privateKey, PublicKey $publicKey)
    {
        $appKey = base64_decode(substr(getenv('APP_KEY'), 7)); // Menghapus 'base64:' dari awal string
        $hasher = new Hash('sha256');
        $encryptor = new AES('cbc');
        $encryptor->setKey($appKey);
        $encryptor->setIV(substr($hasher->hash($appKey), 0, 16));


        $this->digital_signature_private = bin2hex($encryptor->encrypt($privateKey->tostring('PKCS8')));
        $this->digital_signature_public = bin2hex($encryptor->encrypt($publicKey->tostring('PKCS8')));
    }

    public function setOwnSignature(string $private)
    {
        $privateKey = RSA::load($private);
        if (!($privateKey instanceof PrivateKey)) {
            throw new Exception('Error private key...');
        }

        $this->setDigitalSignature($privateKey, $privateKey->getPublicKey());
    }

    public function generateDigitalSignature()
    {
        if (!$this->checkNoKeySignature()) {
            throw new Exception('Already have key signature');
        }

        $privateKey = RSA::createKey();
        $this->setDigitalSignature($privateKey, $privateKey->getPublicKey());
    }

    public function sign(string $data)
    {
        if ($this->checkNoKeySignature()) {
            throw new Exception('No private key');
        }

        $appKey = base64_decode(substr(getenv('APP_KEY'), 7)); // Menghapus 'base64:' dari awal string
        $hasher = new Hash('sha256');
        $encryptor = new AES('cbc');
        $encryptor->setKey($appKey);
        $encryptor->setIV(substr($hasher->hash($appKey), 0, 16));

        $privateKeyString = $encryptor->decrypt(hex2bin($this->digital_signature_private));;
        $privateKey = RSA::load($privateKeyString);
        if (!($privateKey instanceof PrivateKey)) {
            throw new Exception('Error private key...');
        }

        return $privateKey->sign($data);
    }

    public function verify(string $data, string $signature) : bool
    {
        if ($this->checkNoKeySignature()) {
            throw new Exception('No public key');
        }

        $appKey = base64_decode(substr(getenv('APP_KEY'), 7)); // Menghapus 'base64:' dari awal string
        $hasher = new Hash('sha256');
        $encryptor = new AES('cbc');
        $encryptor->setKey($appKey);
        $encryptor->setIV(substr($hasher->hash($appKey), 0, 16));

        $publicKeyString = $encryptor->decrypt(hex2bin($this->digital_signature_public));;
        $publicKey = RSA::load($publicKeyString);
        if (!($publicKey instanceof PublicKey)) {
            throw new Exception('Error public key...');
        }

        return $publicKey->verify($data, $signature);
    }
}
