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
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->float('number_of_workers');
            $table->float('number_of_working_hours_per_day');
            $table->float('number_of_days_under_observation');
            $table->float('number_of_injured_or_sick_workers');
            $table->float('average_number_of_working_days_away_from_work');
            $table->float('number_of_workers_on_leave');
            $table->float('average_number_of_days_spent_on_leave');
            $table->float('average_number_of_overtime_hours_per_day');
            $table->float('average_number_of_overtime_days');
            $table->float('number_of_workers_on_overtime');
            $table->float('hse_meetings_target')->default(0);
            $table->float('hse_meetings_actual')->default(0);
            $table->float('toolbox_talks_target')->default(0);
            $table->float('toolbox_talks_actual')->default(0);
            $table->float('hse_inspection_target')->default(0);
            $table->float('hse_inspection_actual')->default(0);
            $table->float('drills_target')->default(0);
            $table->float('drills_actual')->default(0);
            $table->float('unsafe_acts_target')->default(0);
            $table->float('unsafe_conditions_target')->default(0);
            $table->float('days_interval')->default(0);
            $table->float('total_man_hours')->default(0);
            $table->float('lost_working_hours')->default(0);
            $table->float('leave_hours')->default(0);
            $table->float('total_overtime')->default(0);
            $table->float('actual_man_hour')->default(0);
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
        Schema::dropIfExists('statistics');
    }
};
