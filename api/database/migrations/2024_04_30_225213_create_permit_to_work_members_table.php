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
        Schema::create('permit_to_work_members', function (Blueprint $table) {
            $table->id();
            $table->integer('permit_to_work_id');
            $table->integer('member_id');
            $table->enum('position', ['SUPERVISOR', 'HSE_OFFICER', 'SITE_NURSE', 'WORKER', 'ENTRY_SUPERVISOR', 'ENTRANT', "ATTENDANT"]);
            $table->enum('declaration', ['yes', 'no'])->default('no');
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
        Schema::dropIfExists('permit_to_work_members');
    }
};
