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
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('observation_type_id');
            $table->integer('user_id');
            $table->integer('organization_id')->nullable();
            $table->integer('priority_id');
            $table->enum('status', ['pending investigation', 'being investigated', 'reinvestigating', 'done investigation'])->default('pending investigation');
            $table->text('description');
            $table->text('address');
            $table->text('location_details');
            $table->integer('total_investigations')->nullable();
            $table->integer('affected_workers')->nullable();
            $table->timestamp('incident_datetime');
            $table->enum('is_del', ['yes', 'no'])->default('no');
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
        Schema::dropIfExists('observations');
    }
};
