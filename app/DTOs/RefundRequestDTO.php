<?php

namespace App\DTOs;

class RefundRequestDTO
{
    public function __construct(
        public readonly string $language,
        public readonly string $cardId,
        public readonly string $orderId,
        public readonly float $amount,
        public readonly string $currency,
        public readonly ?string $description = null,
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            language: $data['language'],
            cardId: $data['card_id'],
            orderId: $data['order_id'],
            amount: (float) $data['amount'],
            currency: $data['currency'],
            description: $data['description'] ?? null,
        );
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return array_filter([
            'language' => $this->language,
            'card_id' => $this->cardId,
            'order_id' => $this->orderId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
        ], fn($value) => $value !== null);
    }
}
