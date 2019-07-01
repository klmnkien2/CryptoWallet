<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('wallets')->insert([
            'address' => '0x07a8DBc4aB3350879CC55DAC7b115d4629442c1E',
            'private' => '35AE8B2603C74E3CBC4EA305BE0A9E016EE9B7C8845A27574792DC84A6DF9D29',
            'balance' => '0.00000',
            'user_id' => 0
        ]);
    }
}
