<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(){
        User::insert([
            'name' => 'Global Admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),        
            'remember_token' => Str::random(10),
        ]);   
        User::factory(10)->create();
        // $this->call([
        //     LinkSeeder::class
        // ]);
    }
}
