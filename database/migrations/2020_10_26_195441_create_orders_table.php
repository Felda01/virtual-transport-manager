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
            $table->uuid('id');
            $table->uuid('market_id');
            $table->foreign('market_id')->references('id')->on('markets');
            $table->uuid('driver1_id');
            $table->foreign('driver1_id')->references('id')->on('drivers');
            $table->uuid('driver2_id');
            $table->foreign('driver2_id')->references('id')->on('drivers');
            $table->uuid('truck_id');
            $table->foreign('truck_id')->references('id')->on('trucks');
            $table->uuid('trailer_id');
            $table->foreign('trailer_id')->references('id')->on('trailers');
            $table->uuid('trailer_id');
            $table->foreign('trailer_id')->references('id')->on('trailers');
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
