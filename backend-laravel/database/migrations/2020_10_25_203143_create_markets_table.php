<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('location_from');
            $table->uuid('location_to');
            $table->foreign('location_from')->references('id')->on('locations');
            $table->foreign('location_to')->references('id')->on('locations');
            $table->uuid('cargo_id');
            $table->foreign('cargo_id')->references('id')->on('cargos');
            $table->uuid('customer_from');
            $table->uuid('customer_to');
            $table->foreign('customer_from')->references('id')->on('customers');
            $table->foreign('customer_to')->references('id')->on('customers');
            $table->uuid('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->decimal('price');
            $table->string('frequency');
            $table->integer('amount');
            $table->integer('count_of_repetitions');
            $table->integer('km');
            $table->timestamp('expires_at')->nullable();
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
        Schema::dropIfExists('markets');
    }
}
