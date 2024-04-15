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
        Schema::create('investigation_interviews', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('investigation_id');
            $table->integer('invitee_id');
            $table->enum('type', ['online', 'physical'])->default('online');
            $table->enum('required_action', ['interviewee', 'interviewer'])->default('interviewee');
            $table->timestamp('date_1');
            $table->timestamp('date_2');
            $table->timestamp('date_3');
            $table->timestamp('selected_date')->nullable();
            $table->string('location')->nullable();
            $table->string('link')->nullable();
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
        Schema::dropIfExists('investigation_interviews');
    }
};
