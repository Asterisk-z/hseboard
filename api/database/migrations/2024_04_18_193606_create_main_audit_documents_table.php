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
        Schema::create('main_audit_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->integer('recipient_organization_id');
            $table->integer('recipient_user_id')->nullable();
            $table->integer('audit_id');
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('recipient_comment')->nullable();
            $table->string('user_comment')->nullable();
            // $table->enum('is_accepted', ['yes', 'no'])->default('no');
            $table->enum('is_del', ['yes', 'no'])->default('no');
            $table->enum('status', ['pending', 'uploaded', 'rejected', 'accepted'])->default('pending');
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
        Schema::dropIfExists('main_audit_documents');
    }
};
