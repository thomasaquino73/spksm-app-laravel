<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PesanAmbulanceRequest extends FormRequest
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
            'nama_pasien' => 'required|string|max:255',
            'jenis_kelamin' => 'required',
            'alamat_penjemputan' => 'required|string|max:255',
            'kondisi_pasien' => 'required',
            'lokasi_pengantaran' => 'required',
            
        ];
    }

    public function messages()
    {
        return [
            'nama_pasien.required' => 'Nama pasien harus diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin harus diisi.',
            'alamat_penjemputan.required' => 'Alamat penjemputan harus diisi.',
            'kondisi_pasien.required' => 'Kondisi pasien harus diisi.',
            'lokasi_pengantaran.required' => 'Lokasi pengantaran harus diisi.',
        ];
    }
}
