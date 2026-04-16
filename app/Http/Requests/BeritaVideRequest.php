<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BeritaVideRequest extends FormRequest
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
            'judul' => [
                'required',
                Rule::unique('berita_video', 'judul')
                    ->ignore($this->id, 'id')
                    ->where(fn ($query) => $query->where('status', '<>', 0)),
            ],
            'keyword' => 'required',
            'deskripsi' => 'required',
            'youtube_id' => 'required',
            'kategori_berita_id' => 'required',
        ];
    }
}
