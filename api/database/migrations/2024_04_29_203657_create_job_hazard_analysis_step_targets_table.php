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
        Schema::create('job_hazard_analysis_step_targets', function (Blueprint $table) {
            $table->id();
            $table->integer('job_hazard_analysis_step_id');
            $table->string('code')->nullable();
            $table->string('description')->nullable();

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
        Schema::dropIfExists('job_hazard_analysis_step_targets');
    }
};
