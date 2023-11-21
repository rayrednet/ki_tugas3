<?php

namespace App\Http\Controllers;

use App\Helper\Encryptor;
use App\Http\Requests\Share\RequestShowShareInformasiUser;
use App\Models\InformasiUser;
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

        $keyEnkripsi = $validated['key_user'];

        try  {
            /**
             * @var User
             */
            $user = Auth::user();
            $privateDecryptor = $user->getPrivateEncryptor();
            $keyUserLain = $privateDecryptor->decrypt(hex2bin($keyEnkripsi));

            /**
             * @var User|null
             */
            $userTujuan = User::query()->where('key_enkripsi', '=', $keyUserLain)->first();
            if ($userTujuan === null) {
                return redirect()->back()->withErrors([
                    'error' => 'Key enkripsi salah.'
                ]);
            }

            $profile = [];
            if ($userTujuan->enkripsi_digunakan !== null) {
                $encryptor = new Encryptor($userTujuan->enkripsi_digunakan, $userTujuan->getKeyEnkripsi(), $userTujuan->getIV());
                try {
                    $profile = [
                        'Nama' => $encryptor->decrypt(hex2bin($userTujuan->nama)),
                        'Email' => $encryptor->decrypt(hex2bin($userTujuan->email)),
                        'Tanggal lahir' => $encryptor->decrypt(hex2bin($userTujuan->tanggal_lahir)),
                        'Alamat' => $encryptor->decrypt(hex2bin($userTujuan->alamat)),
                        'Nomor telepon' => $encryptor->decrypt(hex2bin($userTujuan->nomor_telepon)),
                    ];
                }
                catch(Exception $e) {
                    $profile = [
                        'Nama' => '-- Gagal dekripsi --',
                        'Email' => '-- Gagal dekripsi --',
                        'Tanggal lahir' => '-- Gagal dekripsi --',
                        'Alamat' => '-- Gagal dekripsi --',
                        'Nomor telepon' => '-- Gagal dekripsi --',
                    ];
                }
            }
            else {
                $profile = [
                    'Nama' => '-- Belum Diisi --',
                    'Email' => '-- Belum Diisi --',
                    'Tanggal lahir' => '-- Belum Diisi --',
                    'Alamat' => '-- Belum Diisi --',
                    'Nomor telepon' => '-- Belum Diisi --',
                ];
            }

            $daftarInformasi = [];
            $informasiUser = $userTujuan->informasi_user;

            /**
             * @var InformasiUser
             */
            foreach($informasiUser as $informasi) {
                $encryptor = new Encryptor($informasi->enkripsi_digunakan, $userTujuan->getKeyEnkripsi(), $informasi->getIV());
                try {
                    array_push($daftarInformasi, [
                        'nama_informasi' => $encryptor->decrypt(hex2bin($informasi->nama_informasi)),
                        'isi_informasi' => $encryptor->decrypt(hex2bin($informasi->isi_informasi)),
                    ]);
                }
                catch(Exception $e) {
                    array_push($daftarInformasi, [
                        'nama_informasi' => '-- Gagal dekripsi --',
                        'isi_informasi' => '-- Gagal dekripsi --',
                    ]);
                }
            }


            return view('share.informasi.show', [
                'profile' => $profile,
                'daftar_informasi' => $daftarInformasi,
            ]);
        }
        catch(Exception $e) {
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
