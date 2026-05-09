<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomerService;
class CsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      CustomerService::insert([
            ['name' => 'CS 1', 'phone' => '6281299097474'],
            ['name' => 'CS 2', 'phone' => '6281111111111'],
            ['name' => 'CS 3', 'phone' => '6282222222222'],
        ]);
    }
}
