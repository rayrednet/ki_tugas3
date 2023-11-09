<?php

namespace App\Http\Requests\Autentikasi;

use Illuminate\Foundation\Http\FormRequest;

class RequestLogin extends FormRequest
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
            'username' => ['required', 'min:8', 'max:32', 'alpha_num:ascii'],
            'password' => ['required', 'min:8',  'max:64'],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username tidak boleh kosong.',
            'username.min' => 'Panjang username adalah 8-32 huruf.',
            'username.max' => 'Panjang username adalah 8-32 huruf.',
            'username.alpha_num' => 'Username hanya terdiri dari huruf, angka, dan _.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.min' => 'Panjang password adalah 8-64 huruf.',
            'password.max' => 'Panjang password adalah 8-64 huruf.',
        ];
    }
}
