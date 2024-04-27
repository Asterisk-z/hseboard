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
        Schema::create('inspection_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('organization_id')->nullable();
            $table->integer('template_type_id');
            $table->integer('file_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->enum('is_del', ['yes', 'no'])->default('no');
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
        Schema::dropIfExists('inspection_templates');
    }
};
