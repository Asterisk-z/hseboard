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
        Schema::create('main_audits', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->integer('recipient_organization_id');
            $table->integer('audit_type_id');
            $table->integer('audit_template_id');
            $table->string('audit_scope')->nullable();
            $table->string('audit_title');
            $table->timestamp('start_date')->nullable();
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
        Schema::dropIfExists('main_audits');
    }
};
