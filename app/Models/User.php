<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Добавляем для отношения

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
	    'name',
	    'email',
	    'password',
	    'login', // Добавляем
	    'yclients_user_token', // Добавляем
	    'yclients_company_id', // Добавляем
	    'phone', // Добавляем
	    'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
	    'yclients_user_token', // Можно скрыть токен пользователя
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
	        'email_verified_at' => 'datetime',
	        // Убедитесь, что 'password' может быть null, если вы не используете его для авторизации через Laravel Guard
	        'password' => 'hashed', // Если пользователи также могут логиниться по email/password, оставьте. Иначе подумайте.
        ];
    }

	/**
	 * Получить компанию, к которой принадлежит пользователь.
	 */
	public function company(): BelongsTo
	{
		return $this->belongsTo(Company::class);
	}
}
