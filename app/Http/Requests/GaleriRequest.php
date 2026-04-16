<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GaleriRequest extends FormRequest
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
            'caption' => [
                'required',
                Rule::unique('galeri_foto', 'caption')
                    ->ignore($this->id, 'id')
                    ->where(fn ($query) => $query->where('status', '<>', 0)),
            ],
            'keyword' => 'required',
            'photographer_id' => 'required',
            'description' => 'required',
            'kategori_berita_id' => 'required',
        ];
    }
}
