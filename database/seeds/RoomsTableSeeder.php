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
            'name' => 'Orchid A',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Orchid B',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Daisy',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Aster',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Dahlia',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Edelweiss',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Lavender',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Lily',
            'type_id' => 1
        ]);
        DB::table('rooms')->insert([
            'name' => 'Rose',
            'type_id' => 2
        ]);
        DB::table('rooms')->insert([
            'name' => 'Sunflower',
            'type_id' => 2
        ]);
    }
}
