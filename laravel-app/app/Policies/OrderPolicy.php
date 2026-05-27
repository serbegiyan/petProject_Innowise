<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Админ видит все заказы, пользователь — только те, где user_id совпадает.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Просмотр конкретного заказа.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Обычный пользователь видит ТОЛЬКО свой заказ
        return $user->id === $order->user_id;
    }

    /**
     * Редактировать или обновлять статус заказа может только админ.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}
