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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->integer('account_type_id');
            $table->integer('account_role_id')->default(1);
            $table->string('password');
            $table->enum('is_active', ['yes', 'no'])->default('yes');
            $table->enum('is_admin', ['yes', 'no'])->default('no');
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('is_del', ['yes', 'no'])->default('no');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
