<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('location1_id');
            $table->uuid('location2_id');
            $table->foreign('location1_id')->references('id')->on('locations');
            $table->foreign('location2_id')->references('id')->on('locations');
            $table->integer('time');
            $table->integer('distance');
            $table->decimal('fee');
            $table->string('type')->default('truck');
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
        Schema::dropIfExists('routes');
    }
}
