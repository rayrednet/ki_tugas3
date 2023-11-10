<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Hash;
use phpseclib3\Crypt\Random;
use phpseclib3\Crypt\RSA;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "users";

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
        'tanggal_lahir' => 'date',
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
}
