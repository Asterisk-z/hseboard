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
        Schema::create('investigation_questions', function (Blueprint $table) {
            $table->id();
            $table->integer('investigator_id')->nullable();
            $table->integer('investigation_id')->nullable();
            $table->text('question');
            $table->string('type')->default('default');
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
        Schema::dropIfExists('investigation_questions');
    }
};
