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
        Schema::create('investigation_question_users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('responder_id');
            $table->integer('investigation_id');
            $table->integer('question_id');
            $table->text('response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->enum('is_completed', ['yes', 'no'])->default('no');
            $table->enum('is_required', ['yes', 'no'])->default('yes');
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
        Schema::dropIfExists('investigation_question_users');
    }
};
