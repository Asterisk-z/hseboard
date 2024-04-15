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
        Schema::create('investigation_findings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('investigation_id');
            $table->text('description');
            $table->enum('type', ['root', 'immediate', 'conclusion', 'evidence'])->default('root');
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
        Schema::dropIfExists('investigation_findings');
    }
};
