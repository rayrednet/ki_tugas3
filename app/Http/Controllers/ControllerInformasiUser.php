<?php

namespace App\Http\Controllers;

use App\Helper\Encryptor;
use App\Http\Requests\Informasi\RequestStoreInformasiUser;
use App\Http\Requests\Informasi\RequestUpdateInformasiUser;
use App\Models\InformasiUser;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use phpseclib3\Crypt\Random;

class ControllerInformasiUser extends Controller
{

    public function index()
    {
        /**
         * @var User
         */
        $user = Auth::user();
        $daftarInformasiUser = $user->informasi_user;

        $daftarInformasi = [];
        /**
         * @var InformasiUser
         */
        foreach ($daftarInformasiUser as $informasi) {
            try {
                $encryptor = new Encryptor($informasi->enkripsi_digunakan, $user->getKeyEnkripsi(), $informasi->getIV());
                array_push($daftarInformasi, [
                    'id' => $informasi->id,
                    'nama_informasi' =>  $encryptor->decrypt(hex2bin($informasi->nama_informasi)),
                    'isi_informasi' => $encryptor->decrypt(hex2bin($informasi->isi_informasi)),
                ]);
            }
            catch(Exception $e) {
                array_push($daftarInformasi, [
                    'id' => null,
                    'nama_informasi' =>  '-- ERROR! Gagal dekripsi! --',
                    'isi_informasi' => '-- ERROR! Gagal dekripsi! --',
                ]);
            }
        }

        return view('informasi.index', [
            'daftar_informasi' => $daftarInformasi
        ]);
    }

    public function create() {
        return view('informasi.create');
    }

    public function store(RequestStoreInformasiUser $request)
    {
        $validated = $request->validated();

        $namaInformasi = $validated['nama_informasi'];
        $isiInformasi = $validated['isi_informasi'];
        $enkripsiDigunakan = $validated['enkripsi_digunakan'];

        /**
         * @var User
         */
        $user = Auth::user();
        $iv = Random::string(16);

        $encryptor = new Encryptor($enkripsiDigunakan, $user->getKeyEnkripsi(), $iv);
        $namaInformasiEncrypted = bin2hex($encryptor->encrypt($namaInformasi));
        $isiInformasiEncrypted = bin2hex($encryptor->encrypt($isiInformasi));
        $informasiUser = InformasiUser::createInformasiUser($user, $namaInformasiEncrypted, $isiInformasiEncrypted, $enkripsiDigunakan, bin2hex($iv));
        $informasiUser->save();

        return redirect()->route('informasi.index');
    }

    public function edit(string $id)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        /**
         * @var InformasiUser|null
         */
        $informasiUser = $user->informasi_user()->getQuery()->where('id', '=', $id)->first();
        if ($informasiUser === null) {
            return redirect()->back();
        }

        $informasi = [];
        try {
            $encryptor = new Encryptor($informasiUser->enkripsi_digunakan, $user->getKeyEnkripsi(), $informasiUser->getIV());
            $informasi = [
                'id' => $informasiUser->id,
                'nama_informasi' => $encryptor->decrypt(hex2bin($informasiUser->nama_informasi)),
                'isi_informasi' => $encryptor->decrypt(hex2bin($informasiUser->isi_informasi)),
                'enkripsi_digunakan' => $informasiUser->enkripsi_digunakan
            ];
        }
        catch (Exception $e) {
            $informasi = [
                'id' => $informasiUser->id,
                'nama_informasi' => '-- Gagal dekripsi --',
                'isi_informasi' => '-- Gagal dekripsi --',
                'enkripsi_digunakan' => $informasiUser->enkripsi_digunakan
            ];
        }

        return view('informasi.edit', [
            'informasi' => $informasi
        ]);
    }

    public function update(RequestUpdateInformasiUser $request)
    {
        $validated = $request->validated();

        $id = $validated['id'];
        $namaInformasi = $validated['nama_informasi'];
        $isiInformasi = $validated['isi_informasi'];
        $enkripsiDigunakan = $validated['enkripsi_digunakan'];

        /**
         * @var User
         */
        $user = Auth::user();
        $iv = Random::string(16);
        $informasiUser = $user->informasi_user()->getQuery()->where('id', '=', $id)->first();
        if ($informasiUser === null) {
            return redirect()->route('informasi.index');
        }

        $encryptor = new Encryptor($enkripsiDigunakan, $user->getKeyEnkripsi(), $iv);
        $namaInformasiEncrypted = bin2hex($encryptor->encrypt($namaInformasi));
        $isiInformasiEncrypted = bin2hex($encryptor->encrypt($isiInformasi));
        $informasiUser->nama_informasi = $namaInformasiEncrypted;
        $informasiUser->isi_informasi = $isiInformasiEncrypted;
        $informasiUser->iv = bin2hex($iv);
        $informasiUser->enkripsi_digunakan = $enkripsiDigunakan;
        $informasiUser->save();

        return redirect()->route('informasi.index');
    }

    public function delete(string $id)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        /**
         * @var InformasiUser|null
         */
        $informasiUser = $user->informasi_user()->getQuery()->where('id', '=', $id)->first();
        if ($informasiUser === null) {
            return redirect()->back();
        }

        $informasiUser->delete();

        return redirect()->route('informasi.index');
    }
}
