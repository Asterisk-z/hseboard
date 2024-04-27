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
        Schema::create('inspection_question_responses', function (Blueprint $table) {
            $table->id();
            $table->integer('inspection_id');
            $table->integer('inspection_question_id');
            $table->integer('user_id')->nullable();
            $table->integer('organization_id')->nullable();
            $table->integer('recipient_organization_id')->nullable();
            $table->string('comment')->nullable();
            $table->enum('response', ['yes', 'nc_minor', 'na', 'nc_major', 'no'])->default('no');
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
        Schema::dropIfExists('inspection_question_responses');
    }
};
