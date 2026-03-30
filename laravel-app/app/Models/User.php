<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use \Illuminate\Auth\MustVerifyEmail;
    use Notifiable;
    use SoftDeletes;

    const ROLE_ADMIN = 'admin';

    const ROLE_USER = 'user';

    protected $appends = ['role_class', 'role_label'];

    // Проверка на админа
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password', 'role'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function baskets(): HasMany
    {
        return $this->hasMany(Basket::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getRoleClassAttribute()
    {
        return [
            'admin' => 'bg-green-100 text-green-800 border-green-200',
            'user' => 'bg-gray-100 text-gray-800',
        ][$this->role] ?? 'bg-gray-100 text-gray-800';
    }

    public function getRoleLabelAttribute()
    {
        return [
            'admin' => 'Администратор',
            'user' => 'Пользователь',
        ][$this->role] ?? 'Пользователь';
    }
}
