<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoadEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('road_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->integer('delay');
            $table->timestamp('end')->useCurrent();
            $table->uuid('location_from_id');
            $table->foreign('location_from_id')->references('id')->on('locations');
            $table->uuid('location_to_id');
            $table->foreign('location_to_id')->references('id')->on('locations');
            $table->softDeletes();
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
        Schema::dropIfExists('road_events');
    }
}
