<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trailers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trailer_model_id');
            $table->foreign('trailer_model_id')->references('id')->on('trailer_models');
            $table->uuid('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('trailers');
    }
}
