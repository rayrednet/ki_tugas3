<?php

namespace App\Http\Controllers;

use App\Helper\Encryptor;
use App\Http\Requests\File\RequestCreateFile;
use App\Models\FileUser;
use App\Models\User;
use Illuminate\Database\QueryException;
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

        $key = $user->getKeyEnkripsi();

        /**
         * @var FileUser
         */
        foreach($fileUser as $file) {
            $encryptor = new Encryptor($file->enkripsi_digunakan, $key, $file->getIV());
            array_push($daftarFile, [
                'id' => $file->id,
                'nama_file' => $encryptor->decrypt(hex2bin($file->nama_file)),
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

        $iv = Random::string(16);
        $encryptor = new Encryptor($enkripsiDigunakan, $user->getKeyEnkripsi(), $iv);

        /**
         * @var UploadedFile
         */
        foreach($validated['upload'] as $file) {
            $namaFileFisik = Str::uuid();
            $isiFile = file_get_contents($file->getRealPath());
            $isiFileTerenkripsi = $encryptor->encrypt($isiFile);

            Storage::disk('private')->put("files/{$namaFileFisik}", $isiFileTerenkripsi);

            $fileBaru = FileUser::createFileUser(
                $user, bin2hex($encryptor->encrypt($file->getClientOriginalName())),
                "files/{$namaFileFisik}",
                $enkripsiDigunakan, bin2hex($iv)
            );
            $fileBaru->save();
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
         * @var FileUser|null
         */
        $fileUser = $user->file_user()->getQuery()->where('file_user.id', '=', $id)->first();
        if ($fileUser === null) {
            return redirect()->back();
        }

        $encryptor = new Encryptor($fileUser->enkripsi_digunakan, $user->getKeyEnkripsi(), $fileUser->getIV());

        $fileName = $encryptor->decrypt(hex2bin($fileUser->nama_file));

        $isiFileFisikEncrypted = Storage::disk('private')->get($fileUser->nama_file_fisik);
        if ($isiFileFisikEncrypted === null) {
            return redirect()->back();
        }

        $isiFileDecrypted = $encryptor->decrypt($isiFileFisikEncrypted);
        $namaFileSementara = Str::uuid();

        Storage::disk('private')->put("tmp/{$namaFileSementara}", $isiFileDecrypted);

        return response()->download(storage_path("app/private/tmp/{$namaFileSementara}"), $fileName)->deleteFileAfterSend(true);
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
