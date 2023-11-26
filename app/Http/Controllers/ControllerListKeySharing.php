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

        $permintaan = DB::table('key_request')
            ->join('users', 'key_request.user_id_tujuan', '=', 'users.id')
            ->select('users.username', 'key_request.contact', 'key_request.address')
            ->get();

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

        $idTujuan = $request->query('username');
        if ($idTujuan == null) {
            return view('share.index', [
                'key' => null
            ]);
        }

        /**
         * @var User|null
         */
        $userTujuan = User::query()->where('id', '=', $idTujuan)->first();
        if ($userTujuan === null) {
            return redirect()->back()->withErrors([
                'error' => 'User tujuan tidak ada'
            ]);
        } else {
            $user_lain = $userTujuan->username;
        }

        $keyEnkripsi = $user->kirimKeyEnkripsiPada($userTujuan);
        return view('share.sharekey', [
            'key' => $keyEnkripsi,
            'user_lain' => $user_lain
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
