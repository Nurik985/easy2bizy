<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_stat_schedules', function (Blueprint $table) {
            $table->id(); // int UNSIGNED NOT NULL AUTO_INCREMENT
            $table->unsignedInteger('company_id'); // int UNSIGNED NOT NULL
            $table->string('date_work'); // varchar(255) NOT NULL
            $table->unsignedInteger('staff_id')->nullable(); // int UNSIGNED DEFAULT NULL
            $table->unsignedInteger('is_admin')->nullable(); // int UNSIGNED DEFAULT NULL
            $table->unsignedInteger('user_day_count')->nullable(); // int UNSIGNED DEFAULT NULL
            $table->unsignedInteger('user_future_day_count')->nullable(); // int UNSIGNED DEFAULT NULL
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_stat_schedules');
    }
};
