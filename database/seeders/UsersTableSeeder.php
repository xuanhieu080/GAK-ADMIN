<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Bouncer;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $users = User::factory(1)->create(
//            [
//                'first_name' => 'Luke',
//                'last_name' => 'Skywalker',
//                'email' => 'luke@jedi.com',
//                'email_verified_at' => null,
//                'password' => bcrypt('123123')
//            ]
//        );
        $user1 = User::updateOrCreate(
            ['email' => 'lsang088@gmail.com'],
            [
                'first_name' => 'Sáng',
                'last_name' => 'Sáng',
                'name' => 'Sáng Sáng',
                'email_verified_at' => null,
                'password' => bcrypt('lsang088@gmail.com')
            ]
        );
        $user2 = User::updateOrCreate(
            [ 'email' => 'xuanhieu.ht96@gmail.com'],
            [
                'first_name' => 'Phan',
                'last_name' => 'Hiếu',
                'name' => 'Phan Hiếu',
                'email_verified_at' => null,
                'password' => bcrypt('xuanhieu.ht96@gmail.com')
            ]
        );

        Bouncer::assign('admin')->to($user1);
        Bouncer::assign('admin')->to($user2);

//        Bouncer::assign('admin')->to($users->first());
//
//        $others = User::factory(20)->create();
//        foreach ($others as $model) {
//            Bouncer::assign('regular')->to($model);
//        }
    }
}
