<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(['key' => 'Pengumuman'], ['value' => '']);
        Setting::updateOrCreate(['key' => 'Kontak Admin'], ['value' => '']);
    }
}
