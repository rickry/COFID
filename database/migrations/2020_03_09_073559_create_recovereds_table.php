<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecoveredsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recovereds', function (Blueprint $table) {
            $table->id();
            $table->string("country")->index();
            $table->string("country_code");
            $table->integer("latest");
            $table->string("province")->nullable();
            $table->double("lat");
            $table->double("long");
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
        Schema::dropIfExists('recovereds');
    }
}
