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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->integer('recipient_organization_id');
            $table->integer('inspection_type_id');
            $table->integer('inspection_template_id');
            $table->string('facility_name');
            $table->string('location');
            $table->string('description');
            $table->string('objective');
            $table->string('ppe');
            $table->timestamp('start_date');
            $table->enum('declare_proceed', ['yes', 'no'])->default('no');
            $table->timestamp('scheduled_start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->text('status_reason')->nullable();
            $table->enum('status', ['pending', 'accepted', 'completed', 'rejected', 'ongoing'])->default('pending');
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
        Schema::dropIfExists('inspections');
    }
};
