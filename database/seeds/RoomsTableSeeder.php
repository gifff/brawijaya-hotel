<?php

use Illuminate\Database\Seeder;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rooms')->insert([
            'name' => 'Mandi Dalam',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Mandi Luar',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Dahlia',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Beef',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Melati',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Mawar',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Audy',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Anggur',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Jeruk',
            'type_id' => 2
        ]);
        DB::table('rooms')->insert([
            'name' => 'Nanas',
            'type_id' => 2
        ]);
    }
}
