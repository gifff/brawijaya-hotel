<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('room_types')->insert([
            'id' => 1,
            'name' => 'Superior',
            'price' => 400000
        ]);
        DB::table('room_types')->insert([
            'id' => 2,
            'name' => 'Deluxe',
            'price' => 600000
        ]);
        // $this->call('UsersTableSeeder');
        $this->call('RoomsTableSeeder');
    }
}
