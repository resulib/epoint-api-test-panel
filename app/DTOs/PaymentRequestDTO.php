<?php

namespace App\DTOs;

class PaymentRequestDTO
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $language,
        public readonly string $orderId,
        public readonly ?string $description = null,
        public readonly ?int $isInstallment = null,
        public readonly ?string $successRedirectUrl = null,
        public readonly ?string $errorRedirectUrl = null,
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float) $data['amount'],
            currency: $data['currency'],
            language: $data['language'],
            orderId: $data['order_id'],
            description: $data['description'] ?? null,
            isInstallment: isset($data['is_installment']) ? (int) $data['is_installment'] : null,
            successRedirectUrl: $data['success_redirect_url'] ?? null,
            errorRedirectUrl: $data['error_redirect_url'] ?? null,
        );
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return array_filter([
            'amount' => $this->amount,
            'currency' => $this->currency,
            'language' => $this->language,
            'order_id' => $this->orderId,
            'description' => $this->description,
            'is_installment' => $this->isInstallment,
            'success_redirect_url' => $this->successRedirectUrl,
            'error_redirect_url' => $this->errorRedirectUrl,
        ], fn($value) => $value !== null);
    }
}
