<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_histories', function (Blueprint $table) {
            $table->id();
            $table->integer("data_id");
            $table->date("date");
            $table->integer("confirmed");
            $table->integer("deaths");
            $table->integer("recovered");
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
        Schema::dropIfExists('data_histories');
    }
}
