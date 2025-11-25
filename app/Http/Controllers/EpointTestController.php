<?php

namespace App\Http\Controllers;

use App\Services\EpointService;
use Illuminate\Http\Request;

class EpointTestController extends Controller
{
    protected $epointService;

    public function __construct(EpointService $epointService)
    {
        $this->epointService = $epointService;
    }

    /**
     * Show test panel
     */
    public function index()
    {
        $apis = $this->getAvailableApis();
        return view('epoint.index', compact('apis'));
    }

    /**
     * Execute API test
     */
    public function execute(Request $request)
    {
        $apiKey = $request->input('api');
        $params = $request->except(['_token', 'api', 'custom_public_key', 'custom_private_key']);

        // ✅ Custom keys varsa set et
        $customPublicKey = $request->input('custom_public_key');
        $customPrivateKey = $request->input('custom_private_key');

        if (!empty($customPublicKey) && !empty($customPrivateKey)) {
            $this->epointService->setCustomKeys($customPublicKey, $customPrivateKey);
        }

        // Remove empty params
        $params = array_filter($params, function($value) {
            return $value !== null && $value !== '';
        });

        // Convert numeric strings to numbers
        foreach ($params as $key => $value) {
            if (is_numeric($value)) {
                $params[$key] = (float) $value;
            }
        }

        $result = null;

        switch ($apiKey) {
            case 'payment-request':
                $result = $this->epointService->paymentRequest($params);
                break;
            case 'get-status':
                $result = $this->epointService->getStatus($params['transaction']);
                break;
            case 'card-registration':
                $result = $this->epointService->cardRegistration($params);
                break;
            case 'execute-pay':
                $result = $this->epointService->executePay($params);
                break;
            case 'card-registration-with-pay':
                $result = $this->epointService->cardRegistrationWithPay($params);
                break;
            case 'refund-request':
                $result = $this->epointService->refundRequest($params);
                break;
            case 'reverse':
                $result = $this->epointService->reverse($params);
                break;
            case 'split-request':
                $result = $this->epointService->splitRequest($params);
                break;
            case 'split-execute-pay':
                $result = $this->epointService->splitExecutePay($params);
                break;
            case 'pre-auth-request':
                $result = $this->epointService->preAuthRequest($params);
                break;
            case 'pre-auth-complete':
                $result = $this->epointService->preAuthComplete($params);
                break;
            case 'widget-token':
                $result = $this->epointService->createWidgetToken($params);
                break;
            case 'wallet-status':
                $result = $this->epointService->walletStatus();
                break;
            case 'wallet-payment':
                $result = $this->epointService->walletPayment($params);
                break;
            default:
                return back()->with('error', 'Invalid API selected');
        }

        $apis = $this->getAvailableApis();

        return view('epoint.index', [
            'apis' => $apis,
            'result' => $result,
            'selectedApi' => $apiKey,
            'customPublicKey' => $customPublicKey,      // ← Yeni
            'customPrivateKey' => $customPrivateKey,    // ← Yeni
        ]);
    }

    /**
     * Get available APIs with their parameters
     */
    /**
     * Get available APIs with their parameters
     */
    private function getAvailableApis()
    {
        $baseUrl = config('services.epoint.base_url', 'https://epoint.az/api/1');

        return [
            'payment-request' => [
                'name' => 'Payment Request',
                'endpoint' => '/request',
                'full_url' => $baseUrl . '/request',
                'params' => [
                    'amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'currency' => ['type' => 'select', 'required' => true, 'default' => 'AZN', 'options' => ['AZN']],
                    'language' => ['type' => 'select', 'required' => true, 'default' => 'az', 'options' => ['az', 'en', 'ru']],
                    'order_id' => ['type' => 'text', 'required' => true, 'default' => 'TEST_' . time()],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Test payment'],
                    'is_installment' => ['type' => 'select', 'required' => false, 'default' => '0', 'options' => ['0' => 'No', '1' => 'Yes']],
                    'success_redirect_url' => ['type' => 'text', 'required' => false, 'default' => url('/payment/success')],
                    'error_redirect_url' => ['type' => 'text', 'required' => false, 'default' => url('/payment/error')],
                ]
            ],
            'get-status' => [
                'name' => 'Get Status',
                'endpoint' => '/get-status',
                'full_url' => $baseUrl . '/get-status',
                'params' => [
                    'transaction' => ['type' => 'text', 'required' => true, 'default' => 'te000000001'],
                ]
            ],
            'card-registration' => [
                'name' => 'Card Registration',
                'endpoint' => '/card-registration',
                'full_url' => $baseUrl . '/card-registration',
                'params' => [
                    'language' => ['type' => 'select', 'required' => true, 'default' => 'az', 'options' => ['az', 'en', 'ru']],
                    'refund' => ['type' => 'select', 'required' => false, 'default' => '0', 'options' => ['0' => 'Payment', '1' => 'Refund']],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Card registration'],
                ]
            ],
            'execute-pay' => [
                'name' => 'Execute Payment (Saved Card)',
                'endpoint' => '/execute-pay',
                'full_url' => $baseUrl . '/execute-pay',
                'params' => [
                    'language' => ['type' => 'select', 'required' => true, 'default' => 'az', 'options' => ['az', 'en', 'ru']],
                    'card_id' => ['type' => 'text', 'required' => true, 'default' => 'card_123456'],
                    'order_id' => ['type' => 'text', 'required' => true, 'default' => 'TEST_' . time()],
                    'amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'currency' => ['type' => 'select', 'required' => true, 'default' => 'AZN', 'options' => ['AZN']],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Saved card payment'],
                ]
            ],
            'card-registration-with-pay' => [
                'name' => 'Card Registration + Payment',
                'endpoint' => '/card-registration-with-pay',
                'full_url' => $baseUrl . '/card-registration-with-pay',
                'params' => [
                    'language' => ['type' => 'select', 'required' => true, 'default' => 'az', 'options' => ['az', 'en', 'ru']],
                    'order_id' => ['type' => 'text', 'required' => true, 'default' => 'TEST_' . time()],
                    'amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'currency' => ['type' => 'select', 'required' => true, 'default' => 'AZN', 'options' => ['AZN']],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Register card and pay'],
                    'success_redirect_url' => ['type' => 'text', 'required' => false, 'default' => url('/payment/success')],
                    'error_redirect_url' => ['type' => 'text', 'required' => false, 'default' => url('/payment/error')],
                ]
            ],
            'refund-request' => [
                'name' => 'Refund Request',
                'endpoint' => '/refund-request',
                'full_url' => $baseUrl . '/refund-request',
                'params' => [
                    'language' => ['type' => 'select', 'required' => true, 'default' => 'az', 'options' => ['az', 'en', 'ru']],
                    'card_id' => ['type' => 'text', 'required' => true, 'default' => 'card_123456'],
                    'order_id' => ['type' => 'text', 'required' => true, 'default' => 'REFUND_' . time()],
                    'amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'currency' => ['type' => 'select', 'required' => true, 'default' => 'AZN', 'options' => ['AZN']],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Refund payment'],
                ]
            ],
            'reverse' => [
                'name' => 'Reverse Transaction',
                'endpoint' => '/reverse',
                'full_url' => $baseUrl . '/reverse',
                'params' => [
                    'language' => ['type' => 'select', 'required' => true, 'default' => 'az', 'options' => ['az', 'en', 'ru']],
                    'transaction' => ['type' => 'text', 'required' => true, 'default' => 'te000000001'],
                    'amount' => ['type' => 'number', 'required' => false, 'default' => '0.01'],
                    'currency' => ['type' => 'select', 'required' => true, 'default' => 'AZN', 'options' => ['AZN']],
                ]
            ],
            'split-request' => [
                'name' => 'Split Payment Request',
                'endpoint' => '/split-request',
                'full_url' => $baseUrl . '/split-request',
                'params' => [
                    'amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'wallet_id' => ['type' => 'text', 'required' => false, 'default' => ''],
                    'split_user' => ['type' => 'text', 'required' => true, 'default' => 'i000000002'],
                    'split_amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'currency' => ['type' => 'select', 'required' => true, 'default' => 'AZN', 'options' => ['AZN']],
                    'language' => ['type' => 'select', 'required' => true, 'default' => 'az', 'options' => ['az', 'en', 'ru']],
                    'order_id' => ['type' => 'text', 'required' => true, 'default' => 'SPLIT_' . time()],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Split payment'],
                    'success_redirect_url' => ['type' => 'text', 'required' => false, 'default' => url('/payment/success')],
                    'error_redirect_url' => ['type' => 'text', 'required' => false, 'default' => url('/payment/error')],
                ]
            ],
            'split-execute-pay' => [
                'name' => 'Split Execute Pay (Saved Card)',
                'endpoint' => '/split-execute-pay',
                'full_url' => $baseUrl . '/split-execute-pay',
                'params' => [
                    'language' => ['type' => 'select', 'required' => true, 'default' => 'az', 'options' => ['az', 'en', 'ru']],
                    'card_id' => ['type' => 'text', 'required' => true, 'default' => 'card_123456'],
                    'order_id' => ['type' => 'text', 'required' => true, 'default' => 'SPLIT_' . time()],
                    'amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'split_user' => ['type' => 'text', 'required' => true, 'default' => 'i000000002'],
                    'split_amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'currency' => ['type' => 'select', 'required' => true, 'default' => 'AZN', 'options' => ['AZN']],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Split payment with saved card'],
                ]
            ],
            'pre-auth-request' => [
                'name' => 'Pre-auth Request',
                'endpoint' => '/pre-auth-request',
                'full_url' => $baseUrl . '/pre-auth-request',
                'params' => [
                    'amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'currency' => ['type' => 'select', 'required' => true, 'default' => 'AZN', 'options' => ['AZN']],
                    'language' => ['type' => 'select', 'required' => true, 'default' => 'az', 'options' => ['az', 'en', 'ru']],
                    'order_id' => ['type' => 'text', 'required' => true, 'default' => 'PREAUTH_' . time()],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Pre-authorization'],
                    'success_redirect_url' => ['type' => 'text', 'required' => false, 'default' => url('/payment/success')],
                    'error_redirect_url' => ['type' => 'text', 'required' => false, 'default' => url('/payment/error')],
                ]
            ],
            'pre-auth-complete' => [
                'name' => 'Pre-auth Complete',
                'endpoint' => '/pre-auth-complete',
                'full_url' => $baseUrl . '/pre-auth-complete',
                'params' => [
                    'amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'transaction' => ['type' => 'text', 'required' => true, 'default' => 'te000000001'],
                ]
            ],
            'widget-token' => [
                'name' => 'Widget Token (Apple/Google Pay)',
                'endpoint' => '/token/widget',
                'full_url' => $baseUrl . '/token/widget',
                'params' => [
                    'amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'order_id' => ['type' => 'text', 'required' => true, 'default' => 'WIDGET_' . time()],
                    'description' => ['type' => 'text', 'required' => true, 'default' => 'Widget payment'],
                ]
            ],
            'wallet-status' => [
                'name' => 'Wallet Status',
                'endpoint' => '/wallet/status',
                'full_url' => $baseUrl . '/wallet/status',
                'params' => []
            ],
            'wallet-payment' => [
                'name' => 'Wallet Payment',
                'endpoint' => '/wallet/payment',
                'full_url' => $baseUrl . '/wallet/payment',
                'params' => [
                    'wallet_id' => ['type' => 'text', 'required' => true, 'default' => 'wallet_123'],
                    'amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'currency' => ['type' => 'select', 'required' => true, 'default' => 'AZN', 'options' => ['AZN']],
                    'order_id' => ['type' => 'text', 'required' => true, 'default' => 'WALLET_' . time()],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Wallet payment'],
                    'language' => ['type' => 'select', 'required' => true, 'default' => 'az', 'options' => ['az', 'en', 'ru']],
                ]
            ],
        ];
    }




    /**
     * Show checkout test panel
     */
    public function checkoutIndex()
    {
        $checkoutApis = $this->getAvailableCheckoutApis();
        return view('epoint.checkout', compact('checkoutApis'));
    }

    /**
     * Execute Checkout API test
     */
    public function checkoutExecute(Request $request)
    {
        $apiKey = $request->input('api');
        $params = $request->except(['_token', 'api', 'custom_public_key', 'custom_private_key']);

        // Custom keys
        $customPublicKey = $request->input('custom_public_key');
        $customPrivateKey = $request->input('custom_private_key');

        if (!empty($customPublicKey) && !empty($customPrivateKey)) {
            $this->epointService->setCustomKeys($customPublicKey, $customPrivateKey);
        }

        // Remove empty params
        $params = array_filter($params, function($value) {
            return $value !== null && $value !== '';
        });

        // Convert numeric strings
        foreach ($params as $key => $value) {
            if (is_numeric($value)) {
                $params[$key] = (float) $value;
            }
        }

        $result = null;

        switch ($apiKey) {
            case 'checkout-request':
                $result = $this->epointService->checkoutRequest($params);
                break;
            default:
                return back()->with('error', 'Invalid Checkout API selected');
        }

        $checkoutApis = $this->getAvailableCheckoutApis();

        return view('epoint.checkout', [
            'checkoutApis' => $checkoutApis,
            'result' => $result,
            'selectedApi' => $apiKey,
            'customPublicKey' => $customPublicKey,
            'customPrivateKey' => $customPrivateKey,
        ]);
    }

    /**
     * Show invoice test panel
     */
    public function invoiceIndex()
    {
        $invoiceApis = $this->getAvailableInvoiceApis();
        return view('epoint.invoice', compact('invoiceApis'));
    }

    /**
     * Execute Invoice API test
     */
    public function invoiceExecute(Request $request)
    {
        $apiKey = $request->input('api');
        $params = $request->except(['_token', 'api', 'custom_public_key', 'custom_private_key', 'invoice_images']);

        // Custom keys
        $customPublicKey = $request->input('custom_public_key');
        $customPrivateKey = $request->input('custom_private_key');

        if (!empty($customPublicKey) && !empty($customPrivateKey)) {
            $this->epointService->setCustomKeys($customPublicKey, $customPrivateKey);
        }

        // Handle file upload for invoice_images
        if ($request->hasFile('invoice_images')) {
            $params['invoice_images'] = $request->file('invoice_images');
        }

        // Remove empty params
        $params = array_filter($params, function($value) {
            return $value !== null && $value !== '';
        });

        // Convert numeric strings
        foreach ($params as $key => $value) {
            if (is_numeric($value)) {
                $params[$key] = (float) $value;
            }
        }

        $result = null;

        switch ($apiKey) {
            case 'invoice-create':
                $result = $this->epointService->invoiceCreate($params);
                break;
            case 'invoice-update':
                $result = $this->epointService->invoiceUpdate($params);
                break;
            case 'invoice-view':
                $result = $this->epointService->invoiceView($params);
                break;
            case 'invoice-list':
                $result = $this->epointService->invoiceList();
                break;
            case 'invoice-send-sms':
                $result = $this->epointService->invoiceSendSms($params);
                break;
            case 'invoice-send-email':
                $result = $this->epointService->invoiceSendEmail($params);
                break;
            default:
                return back()->with('error', 'Invalid Invoice API selected');
        }

        $invoiceApis = $this->getAvailableInvoiceApis();

        return view('epoint.invoice', [
            'invoiceApis' => $invoiceApis,
            'result' => $result,
            'selectedApi' => $apiKey,
            'customPublicKey' => $customPublicKey,
            'customPrivateKey' => $customPrivateKey,
        ]);
    }

    /**
     * Get available Invoice APIs
     */
    private function getAvailableInvoiceApis()
    {
        $baseUrl = config('services.epoint.base_url', 'https://epoint.az/api/1');

        return [
            'invoice-create' => [
                'name' => 'Create Invoice',
                'endpoint' => '/invoices/create',
                'full_url' => $baseUrl . '/invoices/create',
                'params' => [
                    'sum' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'display' => ['type' => 'select', 'required' => true, 'default' => '1', 'options' => ['0' => 'No', '1' => 'Yes']],
                    'save_as_template' => ['type' => 'select', 'required' => true, 'default' => '0', 'options' => ['0' => 'No', '1' => 'Yes']],
                    'name' => ['type' => 'text', 'required' => false, 'default' => 'Test Customer'],
                    'phone' => ['type' => 'text', 'required' => false, 'default' => '+994501234567'],
                    'email' => ['type' => 'email', 'required' => false, 'default' => 'test@example.com'],
                    'inn' => ['type' => 'text', 'required' => false, 'default' => '1234567890'],
                    'contract_number' => ['type' => 'text', 'required' => false, 'default' => 'CONTRACT_' . time()],
                    'merchant_order_id' => ['type' => 'text', 'required' => false, 'default' => 'ORDER_' . time()],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Test invoice description'],
                    'period_from' => ['type' => 'date', 'required' => false, 'default' => date('Y-m-d')],
                    'period_to' => ['type' => 'date', 'required' => false, 'default' => date('Y-m-d', strtotime('+30 days'))],
                    'invoice_images' => ['type' => 'file', 'required' => false, 'default' => ''],
                ]
            ],
            'invoice-update' => [
                'name' => 'Update Invoice',
                'endpoint' => '/invoices/update',
                'full_url' => $baseUrl . '/invoices/update',
                'params' => [
                    'id' => ['type' => 'number', 'required' => true, 'default' => '1'],
                    'sum' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'display' => ['type' => 'select', 'required' => true, 'default' => '1', 'options' => ['0' => 'No', '1' => 'Yes']],
                    'save_as_template' => ['type' => 'select', 'required' => true, 'default' => '0', 'options' => ['0' => 'No', '1' => 'Yes']],
                    'name' => ['type' => 'text', 'required' => false, 'default' => 'Updated Customer'],
                    'phone' => ['type' => 'text', 'required' => false, 'default' => '+994501234567'],
                    'email' => ['type' => 'email', 'required' => false, 'default' => 'test@example.com'],
                    'inn' => ['type' => 'text', 'required' => false, 'default' => '1234567890'],
                    'contract_number' => ['type' => 'text', 'required' => false, 'default' => 'CONTRACT_' . time()],
                    'merchant_order_id' => ['type' => 'text', 'required' => false, 'default' => 'ORDER_' . time()],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Updated description'],
                    'period_from' => ['type' => 'date', 'required' => false, 'default' => date('Y-m-d')],
                    'period_to' => ['type' => 'date', 'required' => false, 'default' => date('Y-m-d', strtotime('+30 days'))],
                    'invoice_images' => ['type' => 'file', 'required' => false, 'default' => ''],
                ]
            ],
            'invoice-view' => [
                'name' => 'View Invoice',
                'endpoint' => '/invoices/view',
                'full_url' => $baseUrl . '/invoices/view',
                'params' => [
                    'id' => ['type' => 'number', 'required' => true, 'default' => '1'],
                ]
            ],
            'invoice-list' => [
                'name' => 'List Invoices',
                'endpoint' => '/invoices/list',
                'full_url' => $baseUrl . '/invoices/list',
                'params' => []
            ],
            'invoice-send-sms' => [
                'name' => 'Send Invoice via SMS',
                'endpoint' => '/invoices/send-sms',
                'full_url' => $baseUrl . '/invoices/send-sms',
                'params' => [
                    'id' => ['type' => 'number', 'required' => true, 'default' => '1'],
                    'phone' => ['type' => 'text', 'required' => true, 'default' => '+994501234567'],
                ]
            ],
            'invoice-send-email' => [
                'name' => 'Send Invoice via Email',
                'endpoint' => '/invoices/send-email',
                'full_url' => $baseUrl . '/invoices/send-email',
                'params' => [
                    'id' => ['type' => 'number', 'required' => true, 'default' => '1'],
                    'email' => ['type' => 'email', 'required' => true, 'default' => 'test@example.com'],
                ]
            ],
        ];
    }

    /**
     * Get available Checkout APIs
     */
    private function getAvailableCheckoutApis()
    {
        $baseUrl = config('services.epoint.base_url', 'https://epoint.az/api/1');

        return [
            'checkout-request' => [
                'name' => 'Checkout Request',
                'endpoint' => '/checkout',
                'full_url' => $baseUrl . '/checkout',
                'params' => [
                    'amount' => ['type' => 'number', 'required' => true, 'default' => '0.01'],
                    'currency' => ['type' => 'select', 'required' => true, 'default' => 'AZN', 'options' => ['AZN']],
                    'language' => ['type' => 'select', 'required' => true, 'default' => 'az', 'options' => ['az', 'en', 'ru']],
                    'order_id' => ['type' => 'text', 'required' => true, 'default' => 'CHECKOUT_' . time()],
                    'description' => ['type' => 'text', 'required' => false, 'default' => 'Checkout payment'],
                    'is_installment' => ['type' => 'select', 'required' => false, 'default' => '0', 'options' => ['0' => 'No', '1' => 'Yes']],
                    'success_redirect_url' => ['type' => 'text', 'required' => false, 'default' => url('/payment/success')],
                    'error_redirect_url' => ['type' => 'text', 'required' => false, 'default' => url('/payment/error')],
                ]
            ],
        ];
    }
}
