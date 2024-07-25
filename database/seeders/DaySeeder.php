<?php

namespace Database\Seeders;

use App\Models\Day;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Day::create([
            ['code' => 'Mon', 'name' => 'Monday'],
            ['code' => 'Tue', 'name' => 'Tuesday'],
            ['code' => 'Wed', 'name' => 'Wednesday'],
            ['code' => 'Thu', 'name' => 'Thursday'],
            ['code' => 'Fri', 'name' => 'Friday'],
            ['code' => 'Sat', 'name' => 'Saturday'],
            ['code' => 'Sun', 'name' => 'Sunday'],
        ]);
    }
}
