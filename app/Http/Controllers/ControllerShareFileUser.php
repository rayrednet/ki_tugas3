<?php

namespace App\Http\Controllers;

use App\Helper\Encryptor;
use App\Http\Requests\Share\RequestDownloadShareFileUser;
use App\Http\Requests\Share\RequestShowShareFileUser;
use App\Models\FileModel;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ControllerShareFileUser extends Controller
{
    public function index()
    {
        return view('share.file.index');
    }

    public function show(RequestShowShareFileUser $request)
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

            $daftarFile = [];

            /**
             * @var FileModel|null
             */
            $dataFile = FileModel::query()->withWhereHas('key', function($query) use ($keyAkses) {
                $query->where('key', '=', $keyAkses);
            })->get();

            $daftarFile = [];

            /**
             * @var FileModel
             */
            foreach($dataFile as $file) {
                $decrypted = $file->decryptFile();
                array_push($daftarFile, [
                    'id' => $decrypted['id'],
                    'nama_file' => $decrypted['nama_file'],
                ]);
            }


            return view('share.file.show', [
                'key' => $keyEnkripsi,
                'daftar_file' => $daftarFile,
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

    public function download(RequestDownloadShareFileUser $request)
    {
        $validated = $request->validated();
        $id = $validated['id'];
        $keyEnkripsi = $validated['key_akses'];

        try  {
            /**
             * @var User
             */
            $user = Auth::user();
            $privateDecryptor = $user->getPrivateEncryptor();
            $keyAkses = $privateDecryptor->decrypt(hex2bin($keyEnkripsi));

            /**
             * @var FileModel|null
             */
            $fileUser = FileModel::query()->withWhereHas('key', function($query) use ($keyAkses) {
                $query->where('key', '=', $keyAkses);
            })->where('id', '=', $id)->first();
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
