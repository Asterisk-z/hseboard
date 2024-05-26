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
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->integer('assignee_id');
            $table->integer('investigation_id')->nullable();
            $table->integer('inspection_id')->nullable();
            $table->integer('audit_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->integer('priority_id');
            $table->timestamp('start_datetime')->nullable();
            $table->timestamp('end_datetime')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'ongoing', 'canceled', 'completed'])->default('pending');
            $table->text('statusMessage')->nullable();
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
        Schema::dropIfExists('actions');
    }
};
