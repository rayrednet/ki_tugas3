<?php

namespace App\Http\Controllers;

use App\Helper\Encryptor;
use App\Models\FileModel;
use App\Models\InformasiModel;
use App\Models\KeyModel;
use App\Models\ProfileModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Hash;
use phpseclib3\Crypt\Random;

class ControllerShare extends Controller
{
    public function index () {
        /**
         * @var User
         */
        $user = Auth::user();

        $daftarUserLain = User::query()->where('id', '<>', $user->id)->get();
        $daftarUser = [];
        foreach($daftarUserLain as $userLain) {
            array_push($daftarUser, [
                'id' => $userLain->id,
                'username' => $userLain->username
            ]);
        }

        $fileUser = $user->file_user;
        $daftarFile = [];

        /**
         * @var FileModel
         */
        foreach($fileUser as $file) {
            $dataFile = $file->decryptFile();
            array_push($daftarFile, [
                'id' => $dataFile['id'],
                'nama_file' => $dataFile['nama_file'],
            ]);
        }

        $informasiUser = $user->informasi_user;
        $daftarInformasi = [];

        /**
         * @var InformasiModel
         */
        foreach($informasiUser as $informasi) {
            $dataInformasi = $informasi->decryptInformasi();
            array_push($daftarInformasi, [
                'id' => $dataInformasi['id'],
                'nama_informasi' => $dataInformasi['nama_informasi'],
                'isi_informasi' => $dataInformasi['isi_informasi'],
            ]);
        }

        return view('share.index', [
            'daftar_user' => $daftarUser,
            'daftar_file' => $daftarFile,
            'daftar_informasi' => $daftarInformasi,
        ]);
    }

    public function store(Request $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $userId = $request->input('user_id');
        $profile = $request->input('profile') == 'true';
        $informasi = $request->input('informasi') ?? [];
        $file = $request->input('file') ?? [];

        /**
         * @var User|null
         */
        $userTujuan = User::query()->where('id', '=', $userId)->first();
        if ($userTujuan === null) {
            return redirect()->back();
        }

        if ($profile && $user->profile === null) {
            return redirect()->back();
        }

        $daftarInformasiUser = $user->informasi_user()->getQuery()->whereIn('informasi.id', $informasi)->get();
        if (count($daftarInformasiUser) != count($informasi)) {
            return redirect()->back();
        }

        $daftarFileUser = $user->file_user()->getQuery()->whereIn('file.id', $file)->get();
        if (count($daftarFileUser) != count($file)) {
            return redirect()->back();
        }

        $hasher = new Hash('sha256');
        $appKey = base64_decode(substr(getenv('APP_KEY'), 7)); // Menghapus 'base64:' dari awal string
        $encryptorKey = new AES('cbc');
        $encryptorKey->setKey($appKey);
        $encryptorKey->setIV(substr($hasher->hash($appKey), 0, 16));

        $newRandomStringForKey = Random::string(72);
        $hashedKey = $hasher->hash($newRandomStringForKey);
        $encryptedKey = bin2hex($encryptorKey->encrypt($hashedKey));

        if ($profile) {
            /**
             * @var ProfileModel
             */
            $profileUser = $user->profile;
            $decryptedProfileUser = $profileUser->decryptProfile();
            $newProfile = ProfileModel::createProfile($hashedKey, $decryptedProfileUser['nama'],
                $decryptedProfileUser['email'], $decryptedProfileUser['tanggal_lahir'], $decryptedProfileUser['alamat'],
                $decryptedProfileUser['nomor_telepon'], $decryptedProfileUser['enkripsi_digunakan']
            );
            $newProfile->save();
            KeyModel::createKeyModel($encryptedKey, ProfileModel::class, $newProfile->id)->save();
        }

        /**
         * @var InformasiModel
         */
        foreach($daftarInformasiUser as $informasiUser) {
            $decryptedInformasiUser = $informasiUser->decryptInformasi();
            $informasiBaru = InformasiModel::createInformasi($hashedKey, $decryptedInformasiUser['nama_informasi'],
                $decryptedInformasiUser['isi_informasi'], $decryptedInformasiUser['enkripsi_digunakan']
            );
            $informasiBaru->save();
            KeyModel::createKeyModel($encryptedKey, InformasiModel::class, $informasiBaru->id)->save();
        }

        /**
         * @var FileModel
         */
        foreach($daftarFileUser as $fileUser) {
            $dataFile = $fileUser->decryptFile();
            $isiFileFisikEncrypted = Storage::disk('private')->get($fileUser->nama_file_fisik);

            $encryptorFile = new Encryptor($dataFile['enkripsi_digunakan'], $fileUser->key->getKeyEnkripsi(), $fileUser->iv());
            $isiFileDecrypted = $encryptorFile->decrypt($isiFileFisikEncrypted);



            $namaFileFisikBaru = Str::uuid();
            $fileBaru = FileModel::createFile($hashedKey, $dataFile['nama_file'], "files/{$namaFileFisikBaru}", $dataFile['enkripsi_digunakan']);
            $encryptorFileBaru = new Encryptor($dataFile['enkripsi_digunakan'], $hashedKey, $fileBaru->iv());
            $isiFileTerenkripsi = $encryptorFileBaru->encrypt($isiFileDecrypted);

            Storage::disk('private')->put("files/{$namaFileFisikBaru}", $isiFileTerenkripsi);

            $fileBaru->save();
            KeyModel::createKeyModel($encryptedKey, FileModel::class, $fileBaru->id)->save();
        }

        $keyUntukUserTujuan = bin2hex($userTujuan->getPublicEncryptor()->encrypt($encryptedKey));

        return view('share.show', [
            'key' => $keyUntukUserTujuan
        ]);
    }
}
