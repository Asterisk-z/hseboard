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
        Schema::create('permit_to_works', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('issuer_organization_id');
            $table->integer('holder_organization_id');
            $table->integer('permit_to_work_request_type_id');
            $table->integer('issuer_id');
            $table->integer('holder_id');
            $table->integer('jha_id')->nullable();
            $table->string('job_code');
            $table->string('job_title');
            $table->string('description_of_work');
            $table->string('location_name');
            $table->string('contractor_name');
            $table->string('location_id_no');
            $table->timestamp('work_start_time')->nullable();
            $table->timestamp('work_end_time')->nullable();
            $table->timestamp('requested_date')->nullable();
            $table->timestamp('alloted_work_start_time')->nullable();
            $table->timestamp('alloted_work_end_time')->nullable();
            $table->string('comment')->nullable();
            $table->string('final_comment')->nullable();
            $table->enum('send_for_declaration', ['yes', 'no'])->default('no');
            $table->enum('jha_status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->string('jha_comment')->nullable();
            $table->enum('request_form_status', ['pending', 'accepted', 'action_required'])->default('pending');
            $table->string('request_form_comment')->nullable();
            $table->enum('request_status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->enum('process_status', ['ongoing', 'declaration', 'approved', 'issued'])->default('ongoing');
            $table->enum('status', ['pending', 'active', 'cancelled', 'suspended', 'closed', 'completed'])->default('pending');
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
        Schema::dropIfExists('permit_to_works');
    }
};
