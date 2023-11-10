<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestUpdateProfile extends FormRequest
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
            'nama' => ['required'],
            'email' => ['required', 'email'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required'],
            'nomor_telepon' => ['required'],
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
            'nama.required' => 'Nama tidak boleh kosong.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email salah.',
            'tanggal_lahir.required' => 'Tanggal lahir tidak boleh kosong.',
            'tanggal_lahir.date' => 'Format tanggal lahir salah.',
            'alamat.required' => 'Alamat tidak boleh kosong.',
            'nomor_telepon.required' => 'Nomor telepon tidak boleh kosong.',
            'enkripsi_digunakan.required' => 'Enkripsi yang digunakan tidak boleh kosong.',
            'enkripsi_digunakan.in' => 'Enkripsi tidak didukung.',
        ];
    }
}
