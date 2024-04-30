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
        Schema::create('job_hazard_analyses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('organization_id');
            $table->string('title');
            $table->integer('prepared_by');
            $table->timestamp('prepared_date');
            $table->timestamp('completed_at')->nullable();
            $table->integer('reviewed_by')->nullable();
            $table->timestamp('reviewed_date')->nullable();
            $table->text('status_message')->nullable();
            $table->enum('status', ['ongoing', 'completed', 'approved', 'declined'])->default('ongoing');
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
        Schema::dropIfExists('job_hazard_analyses');
    }
};
