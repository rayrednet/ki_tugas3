<?php

namespace App\Http\Controllers;

use App\Helper\Encryptor;
use App\Http\Requests\File\RequestCreateFile;
use App\Models\FileModel;
use App\Models\KeyModel;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpseclib3\Crypt\Random;

class ControllerFile extends Controller
{
    public function index()
    {
        /**
         * @var User
         */
        $user = Auth::user();
        $fileUser = $user->file_user;
        $daftarFile = [];

        /**
         * @var FileModel
         */
        foreach($fileUser as $file) {
            $dataFile = $file->decryptFile();
            array_push($daftarFile, [
                'id' => $file->id,
                'nama_file' => $dataFile['nama_file'],
            ]);
        }

        return view('file.index', [
            'daftar_file' => $daftarFile,
        ]);
    }

    public function create()
    {
        return view('file.create');
    }

    public function store(RequestCreateFile $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        $validated = $request->validated();

        $daftarFileUpload = $validated['upload'];
        $enkripsiDigunakan = $validated['enkripsi_digunakan'];

        $keyUser = $user->getKeyEnkripsi();

        /**
         * @var UploadedFile
         */
        foreach($daftarFileUpload as $file) {
            $namaFileFisik = Str::uuid();
            $fileBaru = FileModel::createFile(
                $keyUser, $file->getClientOriginalName(), "files/{$namaFileFisik}",
                $enkripsiDigunakan
            );


            $encryptor = new Encryptor($enkripsiDigunakan, $user->getKeyEnkripsi(), $fileBaru->iv());
            $isiFile = file_get_contents($file->getRealPath());
            $isiFileTerenkripsi = $encryptor->encrypt($isiFile);
            Storage::disk('private')->put("files/{$namaFileFisik}", $isiFileTerenkripsi);

            $fileBaru->save();

            $keyModel = KeyModel::createKeyModel($user->key_enkripsi, FileModel::class, $fileBaru->id);
            $keyModel->save();
        }
        return redirect()->route('file.index');
    }

    public function show(String $id)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        /**
         * @var FileModel|null
         */
        $fileUser = $user->file_user()->getQuery()->where('file.id', '=', $id)->first();
        if ($fileUser === null) {
            return redirect()->back();
        }

        $dataFile = $fileUser->decryptFile();

        $isiFileFisikEncrypted = Storage::disk('private')->get($fileUser->nama_file_fisik);
        if ($isiFileFisikEncrypted === null) {
            return redirect()->back();
        }

        $encryptor = new Encryptor($dataFile['enkripsi_digunakan'], $fileUser->key->getKeyEnkripsi(), $fileUser->iv());
        $isiFileDecrypted = $encryptor->decrypt($isiFileFisikEncrypted);
        $namaFileSementara = Str::uuid();

        Storage::disk('private')->put("tmp/{$namaFileSementara}", $isiFileDecrypted);

        return response()->download(storage_path("app/private/tmp/{$namaFileSementara}"), $dataFile['nama_file'])->deleteFileAfterSend(true);
    }

    public function delete(String $id)
    {
        /**
         * @var User
         */
        $user = Auth::user();

        /**
         * @var FileUser|null
         */
        $fileUser = $user->file_user()->getQuery()->where('file_user.id', '=', $id)->first();
        if ($fileUser === null) {
            return redirect()->back();
        }

        Storage::disk('private')->delete($fileUser->nama_file_fisik);
        $fileUser->delete();

        return redirect()->back();
    }
}
