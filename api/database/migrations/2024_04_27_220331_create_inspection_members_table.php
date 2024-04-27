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
        Schema::create('inspection_members', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->integer('audit_id');
            $table->enum('position', ['LEAD_INSPECTOR', 'INSPECTION_MEMBER', 'LEAD_REPRESENTATIVE', 'REPRESENTATIVE']);
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
        Schema::dropIfExists('inspection_members');
    }
};
