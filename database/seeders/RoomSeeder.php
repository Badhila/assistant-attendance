<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::create([
            ['code' => 'P', 'name' => 'Pemrograman'],
            ['code' => 'AK', 'name' => 'Aplikasi Komputasi'],
            ['code' => 'AP', 'name' => 'Aplikasi Profesional'],
            ['code' => 'J', 'name' => 'Jaringan Komputer'],
            ['code' => 'SI', 'name' => 'Sistem Informasi'],
        ]);
    }
}
