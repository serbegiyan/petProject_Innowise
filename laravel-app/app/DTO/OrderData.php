<?php

namespace App\DTO;

class OrderData
{
    public function __construct(
        public readonly string $address,
        public readonly string $name,
        public readonly string $phone,
        public readonly string $email,
        public readonly ?string $comment,
        public readonly string $paymentMethod,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            address: $validated['customer_address'],
            name: $validated['customer_name'],
            phone: $validated['customer_phone'],
            email: $validated['customer_email'],
            comment: $validated['comment'] ?? null,
            paymentMethod: $validated['payment_method'] ?? 'cash',
        );
    }
}
