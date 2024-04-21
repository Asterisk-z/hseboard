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
        Schema::create('main_audit_schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('recipient_user_id')->nullable();
            $table->integer('audit_id');
            $table->integer('organization_id')->nullable();
            $table->integer('recipient_organization_id')->nullable();
            $table->timestamp('auditor_time')->nullable();
            $table->timestamp('recipient_time')->nullable();
            $table->timestamp('accepted_time')->nullable();
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
        Schema::dropIfExists('main_audit_schedules');
    }
};
