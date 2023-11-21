<?php

namespace App\Http\Controllers;

use App\Helper\Encryptor;
use App\Http\Requests\Share\RequestDownloadShareFileUser;
use App\Http\Requests\Share\RequestShowShareFileUser;
use App\Models\InformasiUser;
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
            $fileUser = $userTujuan->file_user;

            $daftarFile = [];

            /**
             * @var FileUser
             */
            foreach($fileUser as $file) {
                $encryptor = new Encryptor($file->enkripsi_digunakan, $userTujuan->getKeyEnkripsi(), $file->getIV());
                array_push($daftarFile, [
                    'id' => $file->id,
                    'nama_file' => $encryptor->decrypt(hex2bin($file->nama_file)),
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

            /**
             * @var FileUser|null
             */
            $fileUser = $userTujuan->file_user()->getQuery()->where('file_user.id', '=', $id)->first();
            if ($fileUser === null) {
                return redirect()->back();
            }

            $encryptor = new Encryptor($fileUser->enkripsi_digunakan, $userTujuan->getKeyEnkripsi(), $fileUser->getIV());

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
