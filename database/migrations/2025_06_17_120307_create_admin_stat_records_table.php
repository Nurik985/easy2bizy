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
        Schema::create('admin_stat_records', function (Blueprint $table) {
            $table->id(); // int UNSIGNED NOT NULL
            $table->string('date_record', 30)->index(); // varchar(30) NOT NULL, with BTREE index
            $table->string('date_update', 30); // varchar(30) NOT NULL
            $table->unsignedInteger('company_id'); // int UNSIGNED NOT NULL
            $table->unsignedInteger('staff_id'); // int UNSIGNED NOT NULL
            $table->unsignedInteger('client_id'); // int UNSIGNED NOT NULL
            $table->unsignedInteger('client_is_staff'); // int UNSIGNED NOT NULL
            $table->unsignedInteger('service_count'); // int UNSIGNED NOT NULL
            $table->string('services'); // varchar(255) NOT NULL
            $table->integer('status_record'); // int NOT NULL
            $table->integer('status_service'); // int NOT NULL
            $table->string('date_create', 30)->nullable(); // varchar(30) DEFAULT NULL
            $table->string('date_usage', 30)->default('0'); // varchar(30) DEFAULT '0'
            $table->integer('is_copy')->default(0); // int DEFAULT '0'
            $table->integer('create_in_service')->default(0); // int DEFAULT '0'
            $table->integer('exist_children')->default(0); // int NOT NULL DEFAULT '0'
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_stat_records');
    }
};
