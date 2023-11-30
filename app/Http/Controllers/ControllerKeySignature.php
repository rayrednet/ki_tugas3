<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpseclib3\Crypt\RSA;

class ControllerKeySignature extends Controller {

    public function index()
    {
        /**
         * @var User
         */
        $user = Auth::user();

        try {
            $user->generateDigitalSignature();
            $user->save();
        }
        catch(Exception $e) {}
        return redirect()->route('profile.index');
    }

    public function create()
    {
        return view('key.create');
    }

    public function store(Request $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();
        if (!$user->checkNoKeySignature()) {
            return redirect()->route('profile.index');
        }

        $privateKey = $request->input('private_key');

        try {
            $user->setOwnSignature($privateKey);
            $user->save();
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([
                'Error' => 'Terjadi error penyimpanan key, pastikan key yang anda masukkan benar'
            ]);
        }

        return redirect()->route('profile.index');
    }
}
