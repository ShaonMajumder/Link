<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    function __construct($account_types)
    {
        $this->account_types = $account_types;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $global_password = env('APP_GLOBAL_PASSWORD','12345678');
        
        $users = [
            [
                "name" => "Shaon Majumder",
                "email" => "smazoomder@gmail.com",
                "password" => bcrypt($global_password),
                "account_type" => $this->account_types->where('name','admin')->first()->id
            ],
            [
                "name" => "Global Admin",
                "email" => "admin@admin.com",
                "password" => bcrypt($global_password),
                "account_type" => $this->account_types->where('name','admin')->first()->id
            ]
        ];

        User::insert($users);
        User::factory(10)->create();
        return User::all();
    }
}
