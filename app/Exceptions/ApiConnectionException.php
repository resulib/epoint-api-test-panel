<?php

namespace App\Exceptions;

class ApiConnectionException extends EpointApiException
{
    public static function create(string $endpoint, ?\Throwable $previous = null): self
    {
        return new self(
            message: "API ilə əlaqə qurula bilmədi: {$endpoint}",
            code: 503,
            context: ['endpoint' => $endpoint],
            previous: $previous
        );
    }

    public static function timeout(string $endpoint): self
    {
        return new self(
            message: "API sorğusu zaman aşımına uğradı: {$endpoint}",
            code: 504,
            context: ['endpoint' => $endpoint]
        );
    }

    public static function invalidResponse(string $endpoint, string $reason): self
    {
        return new self(
            message: "API cavabı düzgün deyil: {$reason}",
            code: 502,
            context: [
                'endpoint' => $endpoint,
                'reason' => $reason,
            ]
        );
    }
}
