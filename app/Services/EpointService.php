<?php

namespace App\Services;

use App\Models\EpointLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EpointService
{
    private $publicKey;
    private $privateKey;
    private $baseUrl;
    private $usedCustomKeys = false;

    public function __construct()
    {
        $this->publicKey = config('services.epoint.public_key');
        $this->privateKey = config('services.epoint.private_key');
        $this->baseUrl = config('services.epoint.base_url');
    }

    /**
     * Set custom keys for testing
     */
    public function setCustomKeys($publicKey, $privateKey)
    {
        if (!empty($publicKey) && !empty($privateKey)) {
            $this->publicKey = $publicKey;
            $this->privateKey = $privateKey;
            $this->usedCustomKeys = true;
        }
        return $this;
    }

    /**
     * Generate signature
     */
    private function generateSignature($data)
    {
        $signatureString = $this->privateKey . $data . $this->privateKey;
        return base64_encode(sha1($signatureString, true));
    }

    /**
     * Make API request with logging
     */
    public function makeRequest($endpoint, $params, $apiName = null)
    {
        $startTime = microtime(true);

        // Add public_key to params
        $params['public_key'] = $this->publicKey;

        // Encode data
        $data = base64_encode(json_encode($params));

        // Generate signature
        $signature = $this->generateSignature($data);

        // Make request
        $response = Http::post($this->baseUrl . $endpoint, [
            'data' => $data,
            'signature' => $signature
        ]);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $responseData = $response->json();

        // Extract common fields
        $transactionId = $responseData['transaction'] ?? null;
        $orderId = $params['order_id'] ?? null;
        $amount = $params['amount'] ?? null;
        $status = $responseData['status'] ?? 'unknown';

        // Log to database
        $logEntry = EpointLog::create([
            'api_endpoint' => $endpoint,
            'api_name' => $apiName ?? $endpoint,
            'public_key_used' => $this->publicKey,          // ← Yeni
            'used_custom_keys' => $this->usedCustomKeys,    // ← Yeni
            'request_params' => $params,
            'request_data' => $data,
            'request_signature' => $signature,
            'response_data' => $responseData,
            'response_status_code' => $response->status(),
            'transaction_id' => $transactionId,
            'order_id' => $orderId,
            'amount' => $amount,
            'status' => $status,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'execution_time' => $executionTime,
        ]);

        Log::info('Epoint API Request', [
            'log_id' => $logEntry->id,
            'endpoint' => $endpoint,
            'public_key' => $this->publicKey,
            'custom_keys' => $this->usedCustomKeys,
            'status' => $status,
            'execution_time' => $executionTime . 'ms'
        ]);

        return [
            'log_id' => $logEntry->id,
            'request' => [
                'endpoint' => $endpoint,
                'params' => $params,
                'data' => $data,
                'signature' => $signature,
                'public_key' => $this->publicKey,
                'used_custom_keys' => $this->usedCustomKeys,
            ],
            'response' => $responseData,
            'status_code' => $response->status(),
            'execution_time' => $executionTime
        ];
    }

    /**
     * Payment request
     */
    public function paymentRequest($params)
    {
        return $this->makeRequest('/request', $params, 'Payment Request');
    }

    /**
     * Get payment status
     */
    public function getStatus($transaction)
    {
        return $this->makeRequest('/get-status', [
            'transaction' => $transaction
        ], 'Get Status');
    }

    /**
     * Card registration
     */
    public function cardRegistration($params)
    {
        return $this->makeRequest('/card-registration', $params, 'Card Registration');
    }

    /**
     * Execute payment with saved card
     */
    public function executePay($params)
    {
        return $this->makeRequest('/execute-pay', $params, 'Execute Payment');
    }

    /**
     * Card registration with payment
     */
    public function cardRegistrationWithPay($params)
    {
        return $this->makeRequest('/card-registration-with-pay', $params, 'Card Registration + Payment');
    }

    /**
     * Refund request
     */
    public function refundRequest($params)
    {
        return $this->makeRequest('/refund-request', $params, 'Refund Request');
    }

    /**
     * Reverse transaction
     */
    public function reverse($params)
    {
        return $this->makeRequest('/reverse', $params, 'Reverse Transaction');
    }

    /**
     * Split payment request
     */
    public function splitRequest($params)
    {
        return $this->makeRequest('/split-request', $params, 'Split Payment');
    }

    /**
     * Split execute pay
     */
    public function splitExecutePay($params)
    {
        return $this->makeRequest('/split-execute-pay', $params, 'Split Execute Pay');
    }

    /**
     * Pre-auth request
     */
    public function preAuthRequest($params)
    {
        return $this->makeRequest('/pre-auth-request', $params, 'Pre-auth Request');
    }

    /**
     * Pre-auth complete
     */
    public function preAuthComplete($params)
    {
        return $this->makeRequest('/pre-auth-complete', $params, 'Pre-auth Complete');
    }

    /**
     * Create widget token
     */
    public function createWidgetToken($params)
    {
        return $this->makeRequest('/token/widget', $params, 'Widget Token');
    }

    /**
     * Wallet status
     */
    public function walletStatus()
    {
        return $this->makeRequest('/wallet/status', [], 'Wallet Status');
    }

    /**
     * Wallet payment
     */
    public function walletPayment($params)
    {
        return $this->makeRequest('/wallet/payment', $params, 'Wallet Payment');
    }



    /**
     * Create Invoice
     */
    public function invoiceCreate($params)
    {
        // Handle file upload if exists
        if (isset($params['invoice_images']) && $params['invoice_images'] instanceof \Illuminate\Http\UploadedFile) {
            // For multipart/form-data requests
            return $this->makeMultipartRequest('/invoices/create', $params, 'Create Invoice');
        }

        return $this->makeRequest('/invoices/create', $params, 'Create Invoice');
    }

    /**
     * Update Invoice
     */
    public function invoiceUpdate($params)
    {
        if (isset($params['invoice_images']) && $params['invoice_images'] instanceof \Illuminate\Http\UploadedFile) {
            return $this->makeMultipartRequest('/invoices/update', $params, 'Update Invoice');
        }

        return $this->makeRequest('/invoices/update', $params, 'Update Invoice');
    }

    /**
     * View Invoice
     */
    public function invoiceView($params)
    {
        return $this->makeRequest('/invoices/view', $params, 'View Invoice');
    }

    /**
     * List Invoices
     */
    public function invoiceList()
    {
        return $this->makeRequest('/invoices/list', [], 'List Invoices');
    }

    /**
     * Send Invoice via SMS
     */
    public function invoiceSendSms($params)
    {
        return $this->makeRequest('/invoices/send-sms', $params, 'Send Invoice SMS');
    }

    /**
     * Send Invoice via Email
     */
    public function invoiceSendEmail($params)
    {
        return $this->makeRequest('/invoices/send-email', $params, 'Send Invoice Email');
    }

    /**
     * Make multipart request (for file uploads)
     */
    private function makeMultipartRequest($endpoint, $params, $apiName = null)
    {
        $startTime = microtime(true);

        // Add public_key
        $params['public_key'] = $this->publicKey;

        // Prepare multipart data
        $multipart = [];

        foreach ($params as $key => $value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => fopen($value->getPathname(), 'r'),
                    'filename' => $value->getClientOriginalName(),
                ];
            } else {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value,
                ];
            }
        }

        // Encode data for signature
        $dataParams = $params;
        unset($dataParams['invoice_images']); // Remove file from data encoding
        $data = base64_encode(json_encode($dataParams));

        // Generate signature
        $signature = $this->generateSignature($data);

        // Add signature to multipart
        $multipart[] = [
            'name' => 'data',
            'contents' => $data,
        ];
        $multipart[] = [
            'name' => 'signature',
            'contents' => $signature,
        ];

        // Make request
        $response = Http::asMultipart()->post($this->baseUrl . $endpoint, $multipart);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $responseData = $response->json();

        // Log to database
        $logEntry = EpointLog::create([
            'api_endpoint' => $endpoint,
            'api_name' => $apiName ?? $endpoint,
            'public_key_used' => $this->publicKey,
            'used_custom_keys' => $this->usedCustomKeys,
            'request_params' => $dataParams,
            'request_data' => $data,
            'request_signature' => $signature,
            'response_data' => $responseData,
            'response_status_code' => $response->status(),
            'transaction_id' => null,
            'order_id' => $params['merchant_order_id'] ?? null,
            'amount' => $params['sum'] ?? null,
            'status' => $responseData['status'] ?? 'unknown',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'execution_time' => $executionTime,
        ]);

        return [
            'log_id' => $logEntry->id,
            'request' => [
                'endpoint' => $endpoint,
                'params' => $dataParams,
                'data' => $data,
                'signature' => $signature,
                'public_key' => $this->publicKey,
                'used_custom_keys' => $this->usedCustomKeys,
            ],
            'response' => $responseData,
            'status_code' => $response->status(),
            'execution_time' => $executionTime
        ];
    }
}
