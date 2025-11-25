<?php

namespace App\Exceptions;

class InvalidConfigurationException extends EpointApiException
{
    public static function missingPublicKey(): self
    {
        return new self(
            message: 'Epoint public key konfiqurasiyası tapılmadı',
            code: 500
        );
    }

    public static function missingPrivateKey(): self
    {
        return new self(
            message: 'Epoint private key konfiqurasiyası tapılmadı',
            code: 500
        );
    }

    public static function missingBaseUrl(): self
    {
        return new self(
            message: 'Epoint base URL konfiqurasiyası tapılmadı',
            code: 500
        );
    }

    public static function invalidConfiguration(string $key): self
    {
        return new self(
            message: "Konfiqurasiya düzgün deyil: {$key}",
            code: 500,
            context: ['config_key' => $key]
        );
    }
}
