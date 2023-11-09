<?php

namespace App\Http\Requests\Autentikasi;

use Illuminate\Foundation\Http\FormRequest;

class RequestRegister extends FormRequest
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
            'username' => ['required', 'min:8', 'max:32', 'alpha_num:ascii', 'unique:App\Models\User,username'],
            'password' => ['required', 'min:8',  'max:64'],
            'pengulangan_password' => ['required', 'same:password']
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username tidak boleh kosong.',
            'username.min' => 'Panjang username adalah 8-32 huruf.',
            'username.max' => 'Panjang username adalah 8-32 huruf.',
            'username.alpha_num' => 'Username hanya terdiri dari huruf, angka, dan _.',
            'username.unique' => 'Username telah dipakai.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.min' => 'Panjang password adalah 8-64 huruf.',
            'password.max' => 'Panjang password adalah 8-64 huruf.',
            'pengulangan_password.required' => 'Pengulangan password tidak boleh kosong.',
            'pengulangan_password.same' => 'Pengulangan password tidak sesuai.',
        ];
    }
}
