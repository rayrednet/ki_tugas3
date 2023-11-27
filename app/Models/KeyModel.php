<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Hash;

class KeyModel extends Model
{
    use HasFactory;

    public $table = 'key';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'data',
        'id',
    ];

    public static function createKeyModel(string $key, string $data, string $dataId) : KeyModel
    {
        $keyModel = new KeyModel();
        $keyModel->key = $key;
        $keyModel->data = $data;
        $keyModel->id = $dataId;

        return $keyModel;
    }

    public function getKeyEnkripsi() : string
    {
        $appKey = base64_decode(substr(getenv('APP_KEY'), 7)); // Menghapus 'base64:' dari awal string
        $hasher = new Hash('sha256');
        $encryptor = new AES('cbc');
        $encryptor->setKey($appKey);
        $encryptor->setIV(substr($hasher->hash($appKey), 0, 16));

        return $encryptor->decrypt(hex2bin($this->key));
    }
}
