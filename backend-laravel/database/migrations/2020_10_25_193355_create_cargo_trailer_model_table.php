<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCargoTrailerModelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargo_trailer_model', function (Blueprint $table) {
            $table->uuid('cargo_id');
            $table->uuid('trailer_model_id');
            $table->foreign('cargo_id')->references('id')->on('cargos');
            $table->foreign('trailer_model_id')->references('id')->on('trailer_models');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cargo_trailer');
    }
}
