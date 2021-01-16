<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('gender');
            $table->integer('status');
            $table->integer('adr');
            $table->string('image');
            $table->uuid('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->uuid('truck_id')->nullable();
            $table->foreign('truck_id')->references('id')->on('trucks');
            $table->uuid('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->uuid('garage_id')->nullable();
            $table->foreign('garage_id')->references('id')->on('garages');
            $table->integer('salary');
            $table->integer('satisfaction');
            $table->integer('preferred_road_trips');
            $table->integer('adr')->default(0);
            $table->timestamp('expires_at')->useCurrent();
            $table->timestamp('last_in_garage_at')->nullable();
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
        Schema::dropIfExists('drivers');
    }
}
