<?php

namespace App\Http\Controllers;

use App\Helper\Encryptor;
use App\Http\Requests\Informasi\RequestStoreInformasiUser;
use App\Http\Requests\Informasi\RequestUpdateInformasiUser;
use App\Models\InformasiModel;
use App\Models\KeyModel;
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
         * @var InformasiModel
         */
        foreach ($daftarInformasiUser as $informasi) {
            try {
                $dataInformasiUser = $informasi->decryptInformasi();
                array_push($daftarInformasi, [
                    'id' => $dataInformasiUser['id'],
                    'nama_informasi' =>  $dataInformasiUser['nama_informasi'],
                    'isi_informasi' => $dataInformasiUser['isi_informasi'],
                ]);
            }
            catch(Exception $e) {
                array_push($daftarInformasi, [
                    'id' => '-1',
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
        $informasiModel = InformasiModel::createInformasi($user->getKeyEnkripsi(), $namaInformasi, $isiInformasi, $enkripsiDigunakan);
        $informasiModel->save();

        $keyModel = KeyModel::createKeyModel($user->key_enkripsi, InformasiModel::class, $informasiModel->id);
        $keyModel->save();

        return redirect()->route('informasi.index');
    }

    public function edit(string $id)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        /**
         * @var InformasiModel|null
         */
        $informasiUser = $user->informasi_user()->getQuery()
            ->where('informasi.id', '=', $id)
            ->first();
        if ($informasiUser === null) {
            return redirect()->back();
        }

        $informasi = [];
        try {
            $informasi = $informasiUser->decryptInformasi();
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

        /**
         * @var InformasiModel|null
         */
        $informasiUser = $user->informasi_user()->getQuery()->where('informasi.id', '=', $id)->first();
        if ($informasiUser === null) {
            return redirect()->route('informasi.index');
        }

        $informasiUser->editInformasi($user->getKeyEnkripsi(), $namaInformasi, $isiInformasi, $enkripsiDigunakan);
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
        $informasiUser = $user->informasi_user()->getQuery()->where('informasi.id', '=', $id)->first();
        if ($informasiUser === null) {
            return redirect()->back();
        }

        $keyModel = $informasiUser->key;
        if ($keyModel !== null) {
            $keyModel->delete();
        }

        $informasiUser->delete();

        return redirect()->route('informasi.index');
    }
}
