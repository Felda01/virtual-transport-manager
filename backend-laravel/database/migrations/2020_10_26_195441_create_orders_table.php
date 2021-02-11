<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('market_id');
            $table->foreign('market_id')->references('id')->on('markets');
            $table->uuid('truck_id')->nullable();
            $table->foreign('truck_id')->references('id')->on('trucks');
            $table->uuid('trailer_id')->nullable();
            $table->foreign('trailer_id')->references('id')->on('trailers');
            $table->uuid('road_trip_id')->nullable();
            $table->foreign('road_trip_id')->references('id')->on('road_trips');
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
        Schema::dropIfExists('orders');
    }
}
