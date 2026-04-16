<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenggunaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [

            'no_ID' => 'required|string|max:50',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'daftar_lingkungan_id' => 'required',
            'jenis_kelamin' => [
                'required',
                Rule::in(['Pria', 'Wanita']),
            ],
            'alamat' => 'required|string|max:500',
            // 'daftar_lingkungan_id' => [
            //     'required',
            //     'exists:daftar_lingkungan,id'
            // ],
            'no_telp' => 'required|numeric|digits_between:10,15',
            'warga_negara' => [
                'required',
                Rule::in(['WNI', 'WNA']),
            ],
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($userId),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'status' => [
                'required',
                Rule::in(['Active', 'Not Active']),
            ],

            'roles' => 'required',

            // password
            'password' => $this->isMethod('post')
                ? 'required|string|min:8|same:confirm_password'
                : 'nullable|string|min:8|same:confirm_password',

            'confirm_password' => $this->isMethod('post')
                ? 'required|string|min:8'
                : 'nullable|string|min:8',

        ];
    }

    public function messages(): array
    {
        return [

            'no_ID.required' => 'Nomor ID wajib diisi',
            'no_ID.max' => 'Nomor ID maksimal 50 karakter',

            'avatar.image' => 'Avatar harus berupa gambar',
            'avatar.mimes' => 'Avatar harus berformat jpg, jpeg, atau png',
            'avatar.max' => 'Ukuran avatar maksimal 2MB',

            'nama_lengkap.required' => 'Nama lengkap wajib diisi',

            'tempat_lahir.required' => 'Tempat lahir wajib diisi',

            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',

            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',

            'alamat.required' => 'Alamat wajib diisi',

            'daftar_lingkungan_id.required' => 'Lingkungan wajib dipilih',
            'daftar_lingkungan_id.exists' => 'Lingkungan tidak valid',

            'no_telp.required' => 'Nomor telepon wajib diisi',
            'no_telp.numeric' => 'Nomor telepon harus berupa angka',
            'no_telp.digits_between' => 'Nomor telepon harus 10 - 15 digit',

            'warga_negara.required' => 'Warga negara wajib dipilih',

            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'username.max' => 'Username maksimal 50 karakter',

            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',

            'status.required' => 'Status wajib dipilih',

            'roles.required' => 'Role wajib dipilih',

            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.same' => 'Password dan konfirmasi password harus sama',

            'confirm_password.required' => 'Konfirmasi password wajib diisi',
            'confirm_password.min' => 'Konfirmasi password minimal 6 karakter',

        ];
    }
}
