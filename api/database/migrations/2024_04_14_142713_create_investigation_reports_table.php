<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigation_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('investigation_id');
            $table->string('title');
            $table->text('description');
            $table->text('location');
            $table->timestamp('incident_date_time');
            $table->text('damages');
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
        Schema::dropIfExists('investigation_reports');
    }
};
