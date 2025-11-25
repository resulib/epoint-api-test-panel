<?php

namespace App\DTOs;

class CardRegistrationDTO
{
    public function __construct(
        public readonly string $language,
        public readonly ?int $refund = null,
        public readonly ?string $description = null,
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            language: $data['language'],
            refund: isset($data['refund']) ? (int) $data['refund'] : null,
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
            'refund' => $this->refund,
            'description' => $this->description,
        ], fn($value) => $value !== null);
    }
}
