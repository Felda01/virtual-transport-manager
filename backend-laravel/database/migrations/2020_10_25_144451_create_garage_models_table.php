<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarageModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garage_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('truck_count');
            $table->integer('trailer_count');
            $table->uuid('location_id');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->decimal('insurance');
            $table->decimal('tax');
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('garage_models');
    }
}
