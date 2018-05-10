<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration
{

    public function up()
    {
        Schema::create('reservations', function(Blueprint $table) {
            $table->increments('id');
            // Schema declaration
            $table->string('customer_name', 64)->nullable(false);
            $table->string('customer_nin', 20)->nullable(false);
            $table->string('phone', 12)->nullable(false);
            $table->date('check_in')->nullable(false);
            $table->date('check_out')->nullable(false);

            $table->unsignedInteger('adult_capacity')->nullable(false);
            $table->unsignedInteger('children_capacity')->nullable(false);
            // Constraints declaration
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT check_dates CHECK ("check_in" < "check_out")');

        Schema::create('reservation_rooms', function(Blueprint $table) {
            $table->unsignedInteger('reservation_id')
                ->nullable(false);

            $table->unsignedInteger('room_id')
                ->nullable(false);

            $table->boolean('extra_bed')
                ->nullable(false)
                ->default(false);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            
            $table->primary(['reservation_id', 'room_id']);

            $table->foreign('reservation_id')
                ->references('id')->on('reservations')
                ->onDelete('cascade');

            $table->foreign('room_id')
                ->references('id')->on('rooms')
                ->onDelete('restrict');

        });
    }

    public function down()
    {
        Schema::drop('reservation_rooms');
        Schema::drop('reservations');
    }
}
