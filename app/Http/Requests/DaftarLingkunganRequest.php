<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DaftarLingkunganRequest extends FormRequest
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
            'nama_lingkungan' => [
                'required',
                Rule::unique('daftar_lingkungan', 'nama_lingkungan')
                    ->ignore($this->id, 'id')
                    ->where(fn ($query) => $query->where('status', '1')),
            ],
            'wilayah' => 'required',
            'status' => 'required',
        ];
    }
}
