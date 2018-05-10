<?php

use Illuminate\Database\Seeder;

class ReservationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reservations')->insert([
            'id' => 1,
            'customer_name' => 'Tukiman A',
            'customer_nin' => '165150200111063',
            'phone' => '081213141516',
            'check_in' => date('Y-m-d', time() + (10 * 86400)),
            'check_out' => date('Y-m-d', time() + (13 * 86400)),
            'adult_capacity' => 6,
            'children_capacity' => 9
        ]);
        DB::table('reservation_rooms')->insert([
            'reservation_id' => 1,
            'room_id' => 5,
            'extra_bed' => false,
        ]);
        DB::table('reservation_rooms')->insert([
            'reservation_id' => 1,
            'room_id' => 10,
            'extra_bed' => true,
        ]);

        DB::table('reservations')->insert([
            'id' => 2,
            'customer_name' => 'Tukiman B',
            'customer_nin' => '165150200111064',
            'phone' => '081213141517',
            'check_in' => date('Y-m-d', time() + (3 * 86400)),
            'check_out' => date('Y-m-d', time() + (7 * 86400)),
            'adult_capacity' => 6,
            'children_capacity' => 1
        ]);
        DB::table('reservation_rooms')->insert([
            'reservation_id' => 2,
            'room_id' => 1,
            'extra_bed' => false,
        ]);
        DB::table('reservation_rooms')->insert([
            'reservation_id' => 2,
            'room_id' => 5,
            'extra_bed' => true,
        ]);

        DB::table('reservations')->insert([
            'id' => 3,
            'customer_name' => 'Tukiman M',
            'customer_nin' => '165150200111064',
            'phone' => '081213141517',
            'check_in' => date('Y-m-d', time() - (3 * 86400)),
            'check_out' => date('Y-m-d', time() + (2 * 86400)),
            'adult_capacity' => 6,
            'children_capacity' => 1
        ]);
        DB::table('reservation_rooms')->insert([
            'reservation_id' => 3,
            'room_id' => 1,
            'extra_bed' => false,
        ]);
        DB::table('reservation_rooms')->insert([
            'reservation_id' => 3,
            'room_id' => 5,
            'extra_bed' => true,
        ]);

        DB::table('reservations')->insert([
            'id' => 4,
            'customer_name' => 'Tukiman N',
            'customer_nin' => '165150200111064',
            'phone' => '081213141517',
            'check_in' => date('Y-m-d', time() + (28 * 86400)),
            'check_out' => date('Y-m-d', time() + (35 * 86400)),
            'adult_capacity' => 6,
            'children_capacity' => 1
        ]);
        DB::table('reservation_rooms')->insert([
            'reservation_id' => 4,
            'room_id' => 1,
            'extra_bed' => false,
        ]);
        DB::table('reservation_rooms')->insert([
            'reservation_id' => 4,
            'room_id' => 5,
            'extra_bed' => true,
        ]);

        DB::table('reservations')->insert([
            'id' => 5,
            'customer_name' => 'Tukiman O',
            'customer_nin' => '165150200111064',
            'phone' => '081213141517',
            'check_in' => date('Y-m-d', time() - (5 * 86400)),
            'check_out' => date('Y-m-d', time() - (1 * 86400)),
            'adult_capacity' => 6,
            'children_capacity' => 1
        ]);
        DB::table('reservation_rooms')->insert([
            'reservation_id' => 5,
            'room_id' => 1,
            'extra_bed' => false,
        ]);
        DB::table('reservation_rooms')->insert([
            'reservation_id' => 5,
            'room_id' => 5,
            'extra_bed' => true,
        ]);
    }
}
