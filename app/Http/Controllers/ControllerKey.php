<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpseclib3\Crypt\RSA;

class ControllerKey extends Controller
{
    public function show(Request $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $idTujuan = $request->query('user_id');
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
            $usernameTujuan = $userTujuan->username;
        }

        $keyEnkripsi = $user->kirimKeyEnkripsiPada($userTujuan);
        return view('share.index', [
            'key' => $keyEnkripsi,
            'usernameTujuan' => $usernameTujuan
        ]);
    }

    public function request_key(Request $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $idTujuan = $request->query('user_id');
        if ($idTujuan == null) {
            return view('share.request', [
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
            $usernameTujuan = $userTujuan->username;
        }

        $keyEnkripsi = $user->kirimKeyEnkripsiPada($userTujuan);
        return view('share.request', [
            'key' => $keyEnkripsi,
            'usernameTujuan' => $usernameTujuan
        ]);
    }
}
