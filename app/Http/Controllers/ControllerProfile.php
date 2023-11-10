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

        $encryptedNama = $user->nama;
        $encryptedEmail = $user->email;
        $encryptedTanggalLahir = $user->tanggal_lahir;
        $encryptedAlamat = $user->alamat;
        $encryptedNomorTelepon = $user->nomor_telepon;
        $encryptionUsed = $user->enkripsi_digunakan;
        $iv = $user->iv;

        $profile = [
            'nama' => '-- Belum Diisi --',
            'email' => '-- Belum Diisi --',
            'tanggal_lahir' => '-- Belum Diisi --',
            'alamat' => '-- Belum Diisi --',
            'nomor_telepon' => '-- Belum Diisi --',
        ];

        if ($encryptionUsed != null) {
            try {
                $encryptor = new Encryptor($encryptionUsed, $user->getKeyEnkripsi(), $user->getIV());
                $profile = [
                    'nama' => $encryptor->decrypt(hex2bin($encryptedNama)),
                    'email' => $encryptor->decrypt(hex2bin($encryptedEmail)),
                    'tanggal_lahir' => $encryptor->decrypt(hex2bin($encryptedTanggalLahir)),
                    'alamat' => $encryptor->decrypt(hex2bin($encryptedAlamat)),
                    'nomor_telepon' => $encryptor->decrypt(hex2bin($encryptedNomorTelepon)),
                ];

            }
            catch(Exception $e) {
                $profile = [
                    'nama' => '-- Gagal Dekripsi --',
                    'email' => '-- Gagal Dekripsi --',
                    'tanggal_lahir' => '-- Gagal Dekripsi --',
                    'alamat' => '-- Gagal Dekripsi --',
                    'nomor_telepon' => '-- Gagal Dekripsi --',
                ];
            }
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

        $encryptedNama = $user->nama;
        $encryptedEmail = $user->email;
        $encryptedTanggalLahir = $user->tanggal_lahir;
        $encryptedAlamat = $user->alamat;
        $encryptedNomorTelepon = $user->nomor_telepon;
        $encryptionUsed = $user->enkripsi_digunakan;
        $iv = $user->iv;

        $profile = [
            'nama' => '',
            'email' => '',
            'tanggal_lahir' => '',
            'alamat' => '',
            'nomor_telepon' => '',
            'enkripsi_digunakan' => $encryptionUsed,
        ];

        if ($encryptionUsed != null) {
            try {
                $encryptor = new Encryptor($encryptionUsed, $user->getKeyEnkripsi(), $iv);
                $profile = [
                    'nama' => $encryptor->decrypt(hex2bin($encryptedNama)),
                    'email' => $encryptor->decrypt(hex2bin($encryptedEmail)),
                    'tanggal_lahir' => $encryptor->decrypt(hex2bin($encryptedTanggalLahir)),
                    'alamat' => $encryptor->decrypt(hex2bin($encryptedAlamat)),
                    'nomor_telepon' => $encryptor->decrypt(hex2bin($encryptedNomorTelepon)),
                    'enkripsi_digunakan' => $encryptionUsed,
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
            $iv = Random::string(16);
            $encryptor = new Encryptor($enkripsiDigunakan, $user->getKeyEnkripsi(), $iv);
            $encryptedNama = bin2hex($encryptor->encrypt($nama));
            $encryptedEmail = bin2hex($encryptor->encrypt($email));
            $encryptedTanggalLahir = bin2hex($encryptor->encrypt($tanggalLahir));
            $encryptedAlamat = bin2hex($encryptor->encrypt($alamat));
            $encryptedNomorTelepon = bin2hex($encryptor->encrypt($nomorTelepon));

            $user->setProfile($enkripsiDigunakan, $iv, $encryptedNama, $encryptedEmail, $encryptedTanggalLahir, $encryptedAlamat, $encryptedNomorTelepon);
            $user->save();
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Gagal enkripsi data!'
            ]);
        }

        return redirect()->route('profile.index');
    }
}