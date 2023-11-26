<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpseclib3\Crypt\RSA;
use App\Models\KeySharing;

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

    public function index () {
        $user = Auth::user();

        $userLain = User::query()->where('id', '<>', $user->id)->get()->toArray(); 

        return view('share.index', [
            'key' => null,
            'user_lain' => $userLain
        ]);
    }

    public function store(Request $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $username = $request->input('username');
        $kontak = $request->input('kontak');
        $tujuan = $request->input('tujuan');

        $userTujuan = User::query()->where('username', '=', $username)->first();
        if ($userTujuan === null) {
            dd($request);
            return redirect()->back()->withErrors([
                'error' => 'User tujuan tidak ada'
            ]);
        } else {
            $usernameTujuan = $userTujuan->username;
        }
        
        $keySharing = KeySharing::createRequestKey($user, $userTujuan->id, $kontak, $tujuan);
        $keySharing->save();
        
        return redirect()->back();
    }
}
