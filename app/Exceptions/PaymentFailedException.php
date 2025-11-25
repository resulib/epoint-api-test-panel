<?php

namespace App\Exceptions;

class PaymentFailedException extends EpointApiException
{
    public static function create(string $message, array $context = []): self
    {
        return new self(
            message: "Ödəniş uğursuz oldu: {$message}",
            code: 422,
            context: $context
        );
    }

    public static function invalidAmount(): self
    {
        return new self(
            message: 'Ödəniş məbləği düzgün deyil',
            code: 422
        );
    }

    public static function invalidCurrency(): self
    {
        return new self(
            message: 'Valyuta tipi düzgün deyil',
            code: 422
        );
    }

    public static function orderIdExists(string $orderId): self
    {
        return new self(
            message: "Sifariş nömrəsi artıq mövcuddur: {$orderId}",
            code: 409,
            context: ['order_id' => $orderId]
        );
    }
}
