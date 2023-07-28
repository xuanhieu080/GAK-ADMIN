<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Config::upsert([
            ['code' => 'logo', 'is_file' => 1],
            ['code' => 'name', 'is_file' => 0],
            ['code' => 'address', 'is_file' => 0],
            ['code' => 'phone', 'is_file' => 0],
            ['code' => 'notification', 'is_file' => 0],
            ['code' => 'ico', 'is_file' => 1],
        ],
            ['code'],
            ['is_file']
        );
    }
}
