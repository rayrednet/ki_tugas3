<?php

namespace App\Models;

use App\Helper\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FileUser extends Model
{
    use HasFactory;

    public $table = 'file_user';
    protected $keyType = 'string';

    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'nama_file',
        'nama_file_fisik',
        'enkripsi_digunakan',
        'iv',
    ];

    public static function createFileUser(User $user, String $namaFile, String $namaFileFisik, String $enkripsiDigunakan, String $iv) : FileUser
    {
        $fileUser = new FileUser();
        $fileUser->id = Str::uuid();
        $fileUser->user_id = $user->id;
        $fileUser->nama_file = $namaFile;
        $fileUser->nama_file_fisik = $namaFileFisik;
        $fileUser->enkripsi_digunakan = $enkripsiDigunakan;
        $fileUser->iv = $iv;

        return $fileUser;
    }

    public function getIV() : String
    {
        return hex2bin($this->iv);
    }
}
