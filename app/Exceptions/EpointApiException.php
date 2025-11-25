<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EpointApiException extends Exception
{
    protected array $context;

    public function __construct(
        string $message,
        int $code = 500,
        array $context = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Get exception context
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Report the exception
     */
    public function report(): void
    {
        \Log::error('Epoint API Exception', [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'context' => $this->context,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ]);
    }

    /**
     * Render the exception as an HTTP response
     */
    public function render(Request $request): JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $this->getMessage(),
                    'code' => $this->getCode(),
                    'context' => $this->context,
                ],
            ], $this->getCode());
        }

        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
