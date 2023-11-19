<?php

namespace App\Http\Requests\Informasi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestStoreInformasiUser extends FormRequest
{
/**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_informasi' => ['required', 'max:255'],
            'isi_informasi' => ['required', 'max:255'],
            'enkripsi_digunakan' => [
                'required',
                Rule::in([
                    'aes-cbc', 'aes-cfb', 'aes-ofb', 'aes-ctr',
                    'des-cbc', 'des-cfb', 'des-ofb', 'des-ctr',
                    'rc4'
                ])
            ],
        ];
    }

    public function messages()
    {
        return [
            'nama_informasi.required' => 'Nama informasi tidak boleh kosong.',
            'nama_informasi.max' => 'Panjang maksimum nama informasi adalah 255 karakter.',
            'isi_informasi.required' => 'Isi informasi tidak boleh kosong.',
            'isi_informasi.max' => 'Panjang maksimum isi informasi adalah 255 karakter.',
            'enkripsi_digunakan.required' => 'Enkripsi yang digunakan tidak boleh kosong.',
            'enkripsi_digunakan.in' => 'Enkripsi tidak didukung.',
        ];
    }
}
