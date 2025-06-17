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
        Schema::create('admin_stat_days', function (Blueprint $table) {
            $table->id();
            $table->string('date', 30);
            $table->integer('company_id');
            $table->integer('staff_id');
            $table->integer('count')->nullable();
            $table->integer('future_count')->nullable();
            $table->string('comment')->nullable();
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_stat_days');
    }
};
