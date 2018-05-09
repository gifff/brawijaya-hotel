<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomTypesTable extends Migration
{

    public function up()
    {
        Schema::create('room_types', function(Blueprint $table) {
            $table->increments('id');
            // Schema declaration
            $table->string('name', 32)->nullable(false);
            $table->integer('price')->nullable(false)->default(0);
            // Constraints declaration
            $table->unique('name');
        });
    }

    public function down()
    {
        Schema::drop('room_types');
    }
}
