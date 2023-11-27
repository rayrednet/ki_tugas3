<?php

namespace App\Http\Controllers;

use App\Helper\Encryptor;
use App\Http\Requests\Profile\RequestUpdateProfile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use phpseclib3\Crypt\Random;

class ControllerProfile extends Controller
{
    public function index()
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $profileUser = $user->profile;
        $profile = [];

        if ($profileUser == null) {
            $profile = [
                'ID' => str($user->id),
                'Nama' => '-- Belum Diisi --',
                'Email' => '-- Belum Diisi --',
                'Tanggal Lahir' => '-- Belum Diisi --',
                'Alamat' => '-- Belum Diisi --',
                'Nomor Telepon (WhatsApp)' => '-- Belum Diisi --',
            ];

            return view('profile.index', [
                'profile' => $profile
            ]);
        }

        try {
            $dataProfileUser = $profileUser->decryptProfile();
            $profile = [
                'ID' => str($user->id),
                'Nama' => $dataProfileUser['nama'],
                'Email' => $dataProfileUser['email'],
                'Tanggal Lahir' => $dataProfileUser['tanggal_lahir'],
                'Alamat' => $dataProfileUser['alamat'],
                'Nomor Telepon (WhatsApp)' => $dataProfileUser['nomor_telepon'],
            ];

        }
        catch(Exception $e) {
            $profile = [
                'ID' => str($user->id),
                'Nama' => '-- Gagal Dekripsi --',
                'Email' => '-- Gagal Dekripsi --',
                'Tanggal Lahir' => '-- Gagal Dekripsi --',
                'Alamat' => '-- Gagal Dekripsi --',
                'Nomor Telepon (WhatsApp)' => '-- Gagal Dekripsi --',
            ];
        }

        return view('profile.index', [
            'profile' => $profile
        ]);
    }

    public function edit()
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $profile = [
            'nama' => '',
            'email' => '',
            'tanggal_lahir' => '',
            'alamat' => '',
            'nomor_telepon' => '',
            'enkripsi_digunakan' => '',
        ];

        $profileUser = $user->profile;
        if ($profileUser != null) {
            try {
                $dataProfileUser = $profileUser->decryptProfile();
                $profile = [
                    'nama' => $dataProfileUser['nama'],
                    'email' => $dataProfileUser['email'],
                    'tanggal_lahir' => $dataProfileUser['tanggal_lahir'],
                    'alamat' => $dataProfileUser['alamat'],
                    'nomor_telepon' => $dataProfileUser['nomor_telepon'],
                    'enkripsi_digunakan' => $dataProfileUser['enkripsi_digunakan'],
                ];
            }
            catch(Exception $e) {}
        }

        return view('profile.edit', [
            'profile' => $profile
        ]);
    }

    public function update(RequestUpdateProfile $request)
    {
        $validated = $request->validated();

        $nama = $validated['nama'];
        $email = $validated['email'];
        $tanggalLahir = $validated['tanggal_lahir'];
        $alamat = $validated['alamat'];
        $nomorTelepon = $validated['nomor_telepon'];
        $enkripsiDigunakan = $validated['enkripsi_digunakan'];

        /**
         * @var User
         */
        $user = Auth::user();

        try {
            $user->setProfile($enkripsiDigunakan, $nama, $email, $tanggalLahir, $alamat, $nomorTelepon);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Gagal enkripsi data!'
            ]);
        }

        return redirect()->route('profile.index');
    }
}
