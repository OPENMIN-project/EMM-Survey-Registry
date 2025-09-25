<?php

use App\User;
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
        User::create([
            'name'     => 'Master',
            'email'    => 'master@ethmig.test',
            'role'     => \App\UserRole::MASTER,
            'password' => bcrypt('secret')
        ]);
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@ethmig.test',
            'country'  => 'NO',
            'role'     => \App\UserRole::ADMIN,
            'password' => bcrypt('secret')
        ]);
        User::create([
            'name'     => 'Editor',
            'email'    => 'editor@ethmig.test',
            'country'  => 'NO',
            'role'     => \App\UserRole::EDITOR,
            'password' => bcrypt('secret')
        ]);
        User::create([
            'name'     => 'Ami katherine Saji',
            'email'    => 'amikatherine.saji@sciencespo.fr',
            'country'  => 'FR',
            'role'     => \App\UserRole::ADMIN,
            'password' => bcrypt('temppass')
        ]);
        /*$faker = \Faker\Factory::create();
        $hash = bcrypt('secret');
        for ($i = 1; $i < 100; $i++) {
            User::create([
                'name'       => $faker->name,
                'email'      => $faker->safeEmail,
                'country'    => $faker->countryCode,
                'role'       => \App\UserRole::EDITOR,
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                'password'   => $hash
            ]);
        }*/
    }
}
