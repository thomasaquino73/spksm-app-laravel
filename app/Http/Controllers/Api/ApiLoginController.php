<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class ApiLoginController extends Controller
{
    public function login(Request $request)
    {
        // 1. Ambil input
        $loginInput = $request->input('username'); 
        $password   = $request->input('password');

        // 2. Validasi input kosong
        if (!$loginInput || !$password) {
            return response()->json([
                'message' => 'Login gagal. Username dan password wajib diisi.'
            ], 422);
        }

        // 3. Tentukan field (email atau username)
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 4. Cari User (Langsung gunakan BINARY jika itu username)
        if ($field === 'username') {
            // BINARY memastikan 'Admin' tidak bisa login dengan 'admin'
            $user = User::whereRaw("BINARY username = ?", [$loginInput])->first();
        } else {
            $user = User::where('email', $loginInput)->first();
        }

        // 5. Validasi: Jika user tidak ditemukan ATAU password salah
        // Taruh pengecekan ini SEBELUM memanggil fungsi apa pun dari variabel $user
        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Login gagal. Silakan periksa kembali username dan password Anda.'
            ], 401);
        }

        // 6. Cek Status Aktif
        if ($user->active == 0 || $user->status == 'Not Active') {
            return response()->json([
                'message' => 'Login gagal. Akun Anda tidak aktif, silakan hubungi admin.'
            ], 403);
        }

        // 7. Proses Token (Sekarang aman karena $user sudah pasti ada)
        $user->tokens()->delete();
        $token = $user->createToken('login_token')->plainTextToken;

        // 8. Berikan Response
        return response()->json([
            'message'      => 'Login berhasil',
            'access_token' => $token,
            'user'         => [
                'id'       => $user->id,
                'noID'     => $user->no_ID,
                'nama'     => $user->nama_lengkap,
                'username' => $user->username,
                'email'    => $user->email,
                'avatar'   => $user->avatar,
                'role'     => $user->getRoleNames()->first() ?? 'Member', 
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}