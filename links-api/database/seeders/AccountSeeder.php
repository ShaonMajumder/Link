<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('accounts')->delete();
        $account_types = [
            [ 'name' => 'admin' ],
            [ 'name' => 'user' ]
        ];

        Account::insert($account_types);
        return Account::all();
    }
}
