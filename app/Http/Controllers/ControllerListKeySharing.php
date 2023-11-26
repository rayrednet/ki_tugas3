<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use phpseclib3\Crypt\RSA;
use App\Models\KeySharing;
use Illuminate\Support\Facades\DB;

class ControllerListKeySharing extends Controller
{
    public function show(Request $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $permintaan = DB::table('key_request')
            ->join('users', 'key_request.user_id', '=', 'users.id')
            ->select('users.username', 'key_request.contact', 'key_request.address')
            ->where('users.id', '<>', $user->id)
            ->get();

        // dd($permintaan);

        $idTujuan = $request->query('user_id');
        if ($idTujuan == null) {
            return view('share.listrequest', [
                'key' => null,
                'permintaan' => $permintaan,
            ]);
        }
    }

    public function accept(Request $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $passData = $request->input('username');

        $userTujuan = User::query()->where('username', '=', $passData)->first();
        $kontakTujuan = KeySharing::query()->where('user_id', '=', $userTujuan->id)->first();
        $daftarKontak = $kontakTujuan->toArray();

        // DB::table('key_request')
        //     ->where('user_id', '=', $kontakTujuan)
        //     ->delete();

        DB::table('key_request')
            ->join('users', 'key_request.user_id_tujuan', '=', 'users.id')
            ->where('users.username', '=', $passData)
            ->delete();

        return view('share.sharekey', [
            'key' => $user->kirimKeyEnkripsiPada($userTujuan),
            'daftar_kontak' => $daftarKontak,
        ]);
    }

    public static function decline (Request $request) {
         /**
         * @var User
         */
        $user = Auth::user();

        $deleteData = $request->input('username');

        DB::table('key_request')
            ->join('users', 'key_request.user_id_tujuan', '=', 'users.id')
            ->where('users.username', '=', $deleteData)
            ->delete();
        
        $permintaan = KeySharing::query()->where('user_id_tujuan', '=', $user->id)->get()->toArray();
        
        return view('share.listrequest', [
            'key' => null,
            'permintaan' => $permintaan,
        ]);
    }
}
