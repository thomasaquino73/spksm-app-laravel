<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RumahDukaRequest extends FormRequest
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
    public function rules()
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rumah_duka', 'nama')
                    ->ignore($this->id)
                    ->where(fn ($query) => $query->where('status', 1)),
            ],
        ];
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama rumah duka wajib diisi.',
            'nama.string' => 'Nama rumah duka harus berupa teks.',
            'nama.max' => 'Nama rumah duka tidak boleh lebih dari 255 karakter.',
            'nama.unique' => 'Nama rumah duka sudah digunakan oleh rumah duka lain yang aktif.',
        ];
    }

}
