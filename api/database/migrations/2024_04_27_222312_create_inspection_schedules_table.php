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
        Schema::create('inspection_schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('recipient_user_id')->nullable();
            $table->integer('inspection_id');
            $table->integer('organization_id')->nullable();
            $table->integer('recipient_organization_id')->nullable();
            $table->timestamp('propose_time')->nullable();
            $table->timestamp('suggested_time')->nullable();
            $table->timestamp('accepted_time')->nullable();
            $table->enum('status', ['sent', 'declined', 'accepted'])->default('sent');
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
        Schema::dropIfExists('inspection_schedules');
    }
};
