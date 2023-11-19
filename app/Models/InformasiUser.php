<?php

namespace App\Models;

use App\Helper\Encryptor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InformasiUser extends Model
{
    use HasFactory;

    public $table = 'informasi_user';
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
        'nama_informasi',
        'isi_informasi',
        'enkripsi_digunakan',
        'iv',
    ];

    public static function createInformasiUser(User $user, String $nama_informasi, String $isi_informasi, String $enkripsiDigunakan, String $iv) : InformasiUser
    {
        $informasiUser = new InformasiUser();
        $informasiUser->id = Str::uuid();
        $informasiUser->user_id = $user->id;
        $informasiUser->nama_informasi = $nama_informasi;
        $informasiUser->isi_informasi = $isi_informasi;
        $informasiUser->enkripsi_digunakan = $enkripsiDigunakan;
        $informasiUser->iv = $iv;

        return $informasiUser;
    }

    public function getIV() : String
    {
        return hex2bin($this->iv);
    }
}
