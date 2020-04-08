<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_analytics', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // For some reason the migrations balk when using anything but unsignedBigInteger for foreign keys.
            $table->unsignedBigInteger('property_id');
            $table->foreign('property_id')->references('id')->on('properties');
            $table->unsignedBigInteger('analytic_type_id');
            $table->foreign('analytic_type_id')->references('id')->on('analytic_types');
            $table->string('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_analytics');
    }
}
