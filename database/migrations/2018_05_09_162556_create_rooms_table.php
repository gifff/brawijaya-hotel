<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function(Blueprint $table) {
            $table->increments('id');
            
            // Schema declaration
            $table->string('name', 32)->nullable(false);
            $table->unsignedInteger('type_id');

            // Constraints declaration
            $table->unique('name');
            $table->foreign('type_id')
                ->references('id')->on('room_types')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rooms');
    }
}
