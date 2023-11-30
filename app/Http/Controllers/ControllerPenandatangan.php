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

class ControllerPenandatangan extends Controller
{
    public function index()
    {
        return view('penandatangan.index');
    }

    public function store(Request $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();
        if ($user->checkNoKeySignature()) {
            return redirect()->route('profile.index');
        }

        $filePDF = $request->file('pdf');

        $isiFile = file_get_contents($filePDF->getRealPath());
        $pdf = new PDF($isiFile);

        try {
            $signedPDF = $pdf->putSignature($user);
            $namaFileSementara = Str::uuid();
            Storage::disk('private')->put("tmp/{$namaFileSementara}", $signedPDF);
            return response()->download(storage_path("app/private/tmp/{$namaFileSementara}"), $filePDF->getClientOriginalName())->deleteFileAfterSend(true);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([
                'Error' => 'Gagal menaruh digital signature, pastikan file memiliki digital signature.'
            ]);
        }

        return redirect()->back()->withErrors([
            'Error' => 'Gagal menaruh digital signature, pastikan file memiliki digital signature.'
        ]);
    }
}
