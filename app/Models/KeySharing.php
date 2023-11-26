<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class KeySharing extends Model
{
    use HasFactory;

    public $table = 'key_request';
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
        'user_id_tujuan',
        'contact',
        'address',
    ];

    public static function createRequestKey(User $user, $user_id_tujuan, $contact, $address) : KeySharing
    {
        $keySharing = new KeySharing();
        $keySharing->id = Str::uuid();
        $keySharing->user_id = $user->id;
        $keySharing->user_id_tujuan = $user_id_tujuan;
        $keySharing->contact = $contact;
        $keySharing->address = $address;

        return $keySharing;
    }

    public function peminta() : HasOne
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

}
