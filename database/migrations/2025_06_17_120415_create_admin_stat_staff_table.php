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
        Schema::create('admin_stat_staff', function (Blueprint $table) {
            $table->id(); // int UNSIGNED NOT NULL
            $table->unsignedInteger('company_id'); // int UNSIGNED NOT NULL
            $table->string('phone', 18)->nullable(); // varchar(18) DEFAULT NULL
            $table->string('name'); // varchar(255) NOT NULL
            $table->unsignedInteger('is_admin'); // int UNSIGNED NOT NULL
            $table->string('service_name'); // varchar(255) NOT NULL
            $table->integer('skip')->nullable(); // int DEFAULT NULL
            //$table->timestamps(); // Если нужны created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_stat_staff');
    }
};
