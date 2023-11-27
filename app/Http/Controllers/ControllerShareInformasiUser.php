<?php

namespace App\Http\Controllers;

use App\Helper\Encryptor;
use App\Http\Requests\Share\RequestShowShareInformasiUser;
use App\Models\InformasiModel;
use App\Models\InformasiUser;
use App\Models\KeyModel;
use App\Models\ProfileModel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpseclib3\Crypt\RSA;

class ControllerShareInformasiUser extends Controller
{
    public function index()
    {
        return view('share.informasi.index');
    }

    public function show(RequestShowShareInformasiUser $request)
    {
        $validated = $request->validated();

        $keyEnkripsi = $validated['key_akses'];

        try  {
            /**
             * @var User
             */
            $user = Auth::user();
            $privateDecryptor = $user->getPrivateEncryptor();
            $keyAkses = $privateDecryptor->decrypt(hex2bin($keyEnkripsi));

            $profile = [];
            $daftarInformasi = [];

            /**
             * @var ProfileModel|null
             */
            $dataProfile = ProfileModel::query()->withWhereHas('key', function($query) use ($keyAkses) {
                $query->where('key', '=', $keyAkses);
            })->first();

            if ($dataProfile !== null) {
                $decrypted = $dataProfile->decryptProfile();
                $profile = [
                    'Nama' => $decrypted['nama'],
                    'Email' => $decrypted['email'],
                    'Tanggal lahir' => $decrypted['tanggal_lahir'],
                    'Alamat' => $decrypted['alamat'],
                    'Nomor telepon' => $decrypted['nomor_telepon'],
                ];
            }

            $dataInformasi = InformasiModel::query()->withWhereHas('key', function($query) use ($keyAkses) {
                $query->where('key', '=', $keyAkses);
            })->get();
            /**
             * @var InformasiModel
             */
            foreach($dataInformasi as $data) {
                $decrypted = $data->decryptInformasi();
                array_push($daftarInformasi, [
                    'nama_informasi' => $decrypted['nama_informasi'],
                    'isi_informasi' => $decrypted['isi_informasi'],
                ]);
            }

            return view('share.informasi.show', [
                'profile' => $profile,
                'daftar_informasi' => $daftarInformasi,
            ]);
        }
        catch(Exception $e) {
            dd($e);
            return redirect()->back()->withErrors([
                'error' => 'Key enkripsi salah.'
            ]);
        }

        return redirect()->back()->withErrors([
            'error' => 'Terdapat kesalahan.'
        ]);
    }
}

?>
