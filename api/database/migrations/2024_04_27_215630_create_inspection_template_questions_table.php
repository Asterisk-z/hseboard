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
        Schema::create('inspection_template_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question')->nullable();
            $table->text('title')->nullable();
            $table->integer('inspection_template_id');
            $table->integer('group_id')->nullable();
            $table->integer('header_id')->nullable();
            $table->integer('topic_id')->nullable();
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
        Schema::dropIfExists('inspection_template_questions');
    }
};
