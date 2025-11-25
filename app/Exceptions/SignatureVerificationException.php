<?php

namespace App\Exceptions;

class SignatureVerificationException extends EpointApiException
{
    public static function create(string $message = 'İmza doğrulanması uğursuz oldu'): self
    {
        return new self(
            message: $message,
            code: 401
        );
    }

    public static function invalidSignature(): self
    {
        return new self(
            message: 'Göndərilən imza düzgün deyil',
            code: 401
        );
    }

    public static function missingSignature(): self
    {
        return new self(
            message: 'İmza parametri tapılmadı',
            code: 400
        );
    }
}
