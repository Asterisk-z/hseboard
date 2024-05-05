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
        Schema::create('permit_to_work_action_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('permit_to_work_id')->nullable();
            $table->timestamp('request_date_time')->nullable();
            $table->enum('type', ['REACTIVATION', 'EXTENSION', 'COMPLETION']);
            $table->enum('status', ['PENDING', 'ACCEPTED', 'DECLINED']);
            $table->string('issuer_comment')->nullable();
            $table->string('holder_comment')->nullable();
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
        Schema::dropIfExists('permit_to_work_action_requests');
    }
};
