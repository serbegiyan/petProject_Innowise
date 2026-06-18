<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property UserRole|null $role
 * @property-read string $role_class
 * @property-read string $role_label
 * @property-read Collection<int, Basket> $baskets
 * @property-read Collection<int, Order> $orders
 *
 * @method HasMany<Basket, $this> baskets()
 * @method HasMany<Order, $this> orders()
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;
    use SoftDeletes;

    // Старые константы ROLE_ADMIN и ROLE_USER удаляем

    protected $appends = ['role_class', 'role_label'];

    /**
     * Включаем автоматическое приведение поля role к объекту Enum
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'role' => UserRole::class, // Поле 'role' теперь возвращает Enum
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Проверка на админа стала строгой и типизированной.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Используем новые стрелочные геттеры (Attribute) вместо старых get...Attribute
     */
    protected function roleClass(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->role ?? UserRole::USER)->cssClass()
        );
    }

    protected function roleLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->role ?? UserRole::USER)->label()
        );
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

    /** @return HasMany<Basket, $this> */
    public function baskets(): HasMany
    {
        return $this->hasMany(Basket::class);
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
