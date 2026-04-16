<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class AuthOtpController extends Controller
{
    // 🔥 Kirim OTP
    public function sendOtp(Request $r)
    {
        $r->validate([
            'phone' => 'required'
        ]);

        $otp = rand(100000, 999999);

        $user = User::firstOrCreate(
            ['phone' => $r->phone],
            ['name' => 'User '.$r->phone]
        );

        $user->update([
            'otp' => $otp,
            'otp_expired_at' => Carbon::now()->addMinutes(5)
        ]);

        // ⚠️ SIMULASI (tanpa SMS dulu)
        return response()->json([
            'message' => 'OTP dikirim',
            'otp' => $otp // HAPUS kalau sudah pakai SMS beneran
        ]);
    }

    // 🔥 Verifikasi OTP
    public function verifyOtp(Request $r)
    {
        $r->validate([
            'phone' => 'required',
            'otp' => 'required'
        ]);

        $user = User::where('phone', $r->phone)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->otp != $r->otp) {
            return response()->json(['message' => 'OTP salah'], 400);
        }

        if (now()->gt($user->otp_expired_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        // hapus OTP
        $user->update([
            'otp' => null,
            'otp_expired_at' => null
        ]);

        // buat token
        $token = $user->createToken('otp_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ]);
    }
}
