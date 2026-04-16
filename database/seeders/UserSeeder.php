<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'nama_lengkap' => 'Thomas',
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '1982-03-07',
            'jenis_kelamin' => 'Pria',
            'alamat' => 'Jakarta',
            'role_group_id' => 1,
            'daftar_lingkungan_id' => 1,
            'no_telp' => '081299097474',
            'warga_negara' => 'WNI',
            'email' => 'thomas@gmail.com',
            'password' => Hash::make(1),
            'username' => 'thomas',
            'active' => 1,
            'status' => 'Active',
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);
        $user->assignRole('SuperAdmin');
    }
}
