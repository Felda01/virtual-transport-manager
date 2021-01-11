<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrucksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trucks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('status');
            $table->uuid('truck_model_id');
            $table->foreign('truck_model_id')->references('id')->on('truck_models');
            $table->uuid('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->uuid('trailer_id')->nullable();
            $table->foreign('trailer_id')->references('id')->on('trailers');
            $table->uuid('garage_id');
            $table->foreign('garage_id')->references('id')->on('garages');
            $table->integer('km');
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
        Schema::dropIfExists('trucks');
    }
}
