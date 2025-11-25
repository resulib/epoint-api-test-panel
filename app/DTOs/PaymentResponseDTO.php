<?php

namespace App\DTOs;

class PaymentResponseDTO
{
    public function __construct(
        public readonly int $logId,
        public readonly array $request,
        public readonly array $response,
        public readonly int $statusCode,
        public readonly float $executionTime,
    ) {}

    /**
     * Create DTO from service response
     */
    public static function fromServiceResponse(array $data): self
    {
        return new self(
            logId: $data['log_id'],
            request: $data['request'],
            response: $data['response'],
            statusCode: $data['status_code'],
            executionTime: $data['execution_time'],
        );
    }

    /**
     * Check if payment was successful
     */
    public function isSuccessful(): bool
    {
        return isset($this->response['status']) && $this->response['status'] === 'success';
    }

    /**
     * Get transaction ID if available
     */
    public function getTransactionId(): ?string
    {
        return $this->response['transaction'] ?? null;
    }

    /**
     * Get error message if available
     */
    public function getErrorMessage(): ?string
    {
        return $this->response['error'] ?? $this->response['message'] ?? null;
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return [
            'log_id' => $this->logId,
            'request' => $this->request,
            'response' => $this->response,
            'status_code' => $this->statusCode,
            'execution_time' => $this->executionTime,
        ];
    }
}
