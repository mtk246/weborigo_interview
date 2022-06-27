<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeviceLeasing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_leasing', function (Blueprint $table) {
            $table->id('id');
            $table->string('device_id');
            $table->integer('lease_construction_id');
            $table->integer('lease_max_training')->nullable();
            $table->string('lease_max_date')->nullable();
            $table->string('lease_actual_start_date')->nullable();
            $table->string('lease_next_check')->nullable();
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
        //
    }
}
