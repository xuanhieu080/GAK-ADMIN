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
            ['code' => 'logo', 'is_file' => 1, 'group' => 'default', 'config'=> null],
            ['code' => 'name', 'is_file' => 0, 'group' => 'default', 'config'=> null],
            ['code' => 'address', 'is_file' => 0, 'group' => 'default', 'config'=> null],
            ['code' => 'phone', 'is_file' => 0, 'group' => 'default', 'config'=> null],
            ['code' => 'notification', 'is_file' => 0, 'group' => 'default', 'config'=> null],
            ['code' => 'ico', 'is_file' => 1, 'group' => 'default', 'config'=> null],
            ['code' => 'MAIL_HOST', 'is_file' => 0, 'group' => 'email', 'config'=> 'mail.mailers.smtp.host'],
            ['code' => 'MAIL_PORT', 'is_file' => 0, 'group' => 'email', 'config'=> 'mail.mailers.smtp.port'],
            ['code' => 'MAIL_USERNAME', 'is_file' => 0, 'group' => 'email', 'config'=> 'mail.mailers.smtp.username'],
            ['code' => 'MAIL_PASSWORD', 'is_file' => 0, 'group' => 'email', 'config'=> 'mail.mailers.smtp.password'],
            ['code' => 'MAIL_ENCRYPTION', 'is_file' => 0, 'group' => 'email', 'config'=> 'mail.mailers.smtp.encryption'],
            ['code' => 'MAIL_FROM_NAME', 'is_file' => 0, 'group' => 'email', 'config'=> 'mail.from.name'],
            ['code' => 'MAIL_FROM_ADDRESS', 'is_file' => 0, 'group' => 'email', 'config'=> 'mail.from.address'],
//            ['code' => 'EMAIL_RECEIVE_CONTACT', 'is_file' => 0, 'group' => 'email', 'config'=> 'mail.mailers.host'],
        ],
            ['code'],
            ['is_file', 'group', 'config']
        );
    }
}
