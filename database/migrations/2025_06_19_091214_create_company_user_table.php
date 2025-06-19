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
        Schema::create('company_user', function (Blueprint $table) {
            // Внешние ключи
            $table->foreignId('user_id')
                ->constrained('users') // <-- Важно! Укажите имя таблицы, к которой относится foreignId.
                // Если вы переименовали App\Models\User в YClientsUser и таблица называется users,
                // то 'users'. Если таблица называется yclients_users, то 'yclients_users'.
                ->onDelete('cascade');

            $table->foreignId('company_id')
                ->constrained('companies') // <-- Важно! Укажите имя таблицы компаний.
                ->onDelete('cascade');

            // Определяем составной первичный ключ
            // Это предотвращает дублирование одной и той же связи (например, user 1 к company 5 дважды)
            $table->primary(['user_id', 'company_id'], 'yclients_user_company_primary');

            // Если вам нужны временные метки (created_at, updated_at) в pivot-таблице,
            // добавьте их здесь. Laravel не добавляет их по умолчанию для pivot-таблиц,
            // но поддерживает их через метод ->withTimestamps() в BelongsToMany.
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_user');
    }
};
