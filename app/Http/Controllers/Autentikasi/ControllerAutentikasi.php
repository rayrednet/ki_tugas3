<?php

namespace App\Http\Controllers\Autentikasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Autentikasi\RequestLogin;
use App\Http\Requests\Autentikasi\RequestRegister;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControllerAutentikasi extends Controller
{
    public function index()
    {
        return view('autentikasi.index');
    }

    public function register(RequestRegister $request)
    {
        /**
         * @var array
         */
        $validated = $request->validated();

        $username = $validated['username'];
        $password = $validated['password'];

        $userBaru = User::createUser($username, $password);
        $userBaru->save();

        return redirect()->route('autentikasi.index');
    }

    public function login(RequestLogin $request)
    {
        /**
         * @var array
         */
        $validated = $request->validated();

        $username = $validated['username'];
        $password = $validated['password'];

        if (!Auth::attempt(['username' => $username, 'password' => $password])) {
            return redirect()->back()->withErrors([
                'login' => 'Terdapat kesalahan pada username dan/atau password.'
            ]);
        }

        return redirect()->route('profile.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('autentikasi.index');
    }
}
