<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestCreateFile extends FormRequest
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
            'upload' => ['required', 'array'],
            'upload.*' => ['required'],
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
            'upload.required' => 'Tidak ada file yang di upload.',
            'upload.array' => 'Error format upload file.',
            'upload.*.required' => 'Kesalahan pada file yang diupload.',
            'enkripsi_digunakan.required' => 'Enkripsi yang digunakan tidak boleh kosong.',
            'enkripsi_digunakan.in' => 'Enkripsi tidak didukung.',
        ];
    }
}
