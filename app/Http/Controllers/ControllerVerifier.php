<?php

namespace App\Http\Controllers;

use App\Helper\Encryptor;
use App\Helper\PDF;
use App\Http\Requests\Profile\RequestUpdateProfile;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ControllerVerifier extends Controller
{
    public function index()
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $daftarUserLain = User::query()->get();
        $daftarUser = [];
        foreach($daftarUserLain as $userLain) {
            array_push($daftarUser, [
                'id' => $userLain->id,
                'username' => $userLain->username
            ]);
        }

        return view('verifier.index', [
            'daftar_user' => $daftarUser,
        ]);
    }

    public function store(Request $request)
    {
        $idUserTujuan = $request->input('user_id');
        /**
         * @var User|null
         */
        $userTujuan = User::query()->where('id', '=', $idUserTujuan)->first();

        if ($userTujuan === null) {
            return view('verifier.show');
        }

        $filePDF = $request->file('pdf');

        $isiFile = file_get_contents($filePDF->getRealPath());
        $pdf = new PDF($isiFile);

        try {
            if($pdf->checkSignature($userTujuan)) {
                return view('verifier.show', [
                    'verifier' => true,
                    'username' => $userTujuan->username,
                ]);
            }
            return view('verifier.show', [
                'verifier' => false,
                'username' => $userTujuan->username,
            ]);
        }
        catch(Exception $e) {
            return view('verifier.show', [
                'verifier' => false,
                'username' => $userTujuan->username,
            ]);
        }
    }
}
