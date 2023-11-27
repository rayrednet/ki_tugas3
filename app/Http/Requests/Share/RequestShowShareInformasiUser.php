<?php

namespace App\Http\Requests\Share;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestShowShareInformasiUser extends FormRequest
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
            'key_akses' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'key_akses.required' => 'Key tidak boleh kosong.',
        ];
    }
}
