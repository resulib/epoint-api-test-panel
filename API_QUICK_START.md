# Epoint API - Tez Başlanğıc Bələdçisi

## 🚀 5 Dəqiqədə İnteqrasiya

### Addım 1: Environment Dəyişənlərini Təyin Et

`.env` faylınızda:

```env
EPOINT_PUBLIC_KEY=your_public_key_here
EPOINT_PRIVATE_KEY=your_private_key_here
EPOINT_BASE_URL=https://epoint.az/api/1
```

### Addım 2: EpointService İnjeksiyası

```php
use App\Services\EpointService;

class PaymentController extends Controller
{
    public function __construct(
        protected EpointService $epointService
    ) {}
}
```

### Addım 3: Ödəniş Sorğusu Göndər

```php
public function createPayment(Request $request)
{
    $result = $this->epointService->paymentRequest([
        'amount' => 10.50,
        'currency' => 'AZN',
        'language' => 'az',
        'order_id' => 'ORDER_' . time(),
        'description' => 'Məhsul alışı',
        'success_redirect_url' => route('payment.success'),
        'error_redirect_url' => route('payment.error'),
    ]);

    if ($result['response']['status'] === 'success') {
        return redirect($result['response']['payment_url']);
    }

    return back()->withErrors('Ödəniş sorğusu uğursuz oldu');
}
```

### Addım 4: Status Yoxla

```php
public function checkStatus($transactionId)
{
    $result = $this->epointService->getStatus($transactionId);

    if ($result['response']['payment_status'] === 'paid') {
        // Ödəniş uğurlu
        return view('payment.success');
    }

    return view('payment.pending');
}
```

---

## 📱 Əsas İstifadə Halları

### 1. Sadə Ödəniş Flow

```php
// 1. Payment yaradırsan
$payment = $epointService->paymentRequest($params);

// 2. İstifadəçini yönləndirirsən
redirect($payment['response']['payment_url']);

// 3. Callback-də status yoxlayırsan
$status = $epointService->getStatus($transactionId);
```

### 2. Saxlanmış Kartla Ödəniş

```php
// 1. Kartı qeydiyyatdan keçir
$registration = $epointService->cardRegistration([
    'language' => 'az'
]);

// 2. Kartla ödəniş et
$payment = $epointService->executePay([
    'card_id' => $savedCardId,
    'amount' => 10.50,
    'currency' => 'AZN',
    'order_id' => 'ORDER_123',
]);
```

### 3. Geri Qaytarma

```php
$refund = $epointService->refundRequest([
    'language' => 'az',
    'card_id' => $cardId,
    'order_id' => 'REFUND_' . time(),
    'amount' => 10.50,
    'currency' => 'AZN',
]);
```

### 4. Faktura Yaratma

```php
$invoice = $epointService->invoiceCreate([
    'sum' => 100.00,
    'display' => 1,
    'name' => 'Müştəri Adı',
    'phone' => '+994501234567',
    'email' => 'customer@example.com',
    'description' => 'Xidmət haqqı',
]);

// İnvoice-i SMS ilə göndər
$epointService->invoiceSendSms([
    'id' => $invoice['response']['invoice_id'],
    'phone' => '+994501234567',
]);
```

---

## 🔧 FormRequest və DTO İstifadəsi

### FormRequest ilə Validasiya

```php
use App\Http\Requests\PaymentRequestFormRequest;

public function createPayment(PaymentRequestFormRequest $request)
{
    // Validasiya avtomatik aparılır
    $validated = $request->validated();

    $result = $this->epointService->paymentRequest($validated);
}
```

### DTO ilə İşləmək

```php
use App\DTOs\PaymentRequestDTO;
use App\DTOs\PaymentResponseDTO;

// Request DTO
$dto = PaymentRequestDTO::fromArray($request->validated());
$result = $epointService->paymentRequest($dto->toArray());

// Response DTO
$responseDto = PaymentResponseDTO::fromServiceResponse($result);

if ($responseDto->isSuccessful()) {
    $transactionId = $responseDto->getTransactionId();
}
```

---

## ⚠️ Ən Çox Rast Gəlinən Xətalar

### 1. Signature Xətası (401)

```php
// ❌ Səhv
$params = ['amount' => '10.50']; // String

// ✅ Düzgün
$params = ['amount' => 10.50]; // Float
```

### 2. Validasiya Xətası (422)

```php
// ❌ Səhv - order_id unikal deyil
$params = ['order_id' => 'TEST_123'];

// ✅ Düzgün - hər dəfə unikal
$params = ['order_id' => 'TEST_' . time() . '_' . uniqid()];
```

### 3. Rate Limit Aşılması (429)

```php
// Rate limit yoxlama
try {
    $result = $epointService->paymentRequest($params);
} catch (ApiConnectionException $e) {
    if ($e->getCode() === 429) {
        // 1 dəqiqə gözlə
        sleep(60);
        retry();
    }
}
```

---

## 🧪 Testing

### Unit Test Nümunəsi

```php
use Tests\TestCase;
use App\Services\EpointService;

class PaymentTest extends TestCase
{
    public function test_payment_request_succeeds()
    {
        Http::fake([
            '*' => Http::response([
                'status' => 'success',
                'transaction' => 'te000000001',
            ], 200)
        ]);

        $service = new EpointService();
        $result = $service->paymentRequest([
            'amount' => 10.50,
            'currency' => 'AZN',
            'language' => 'az',
            'order_id' => 'TEST_' . time(),
        ]);

        $this->assertEquals('success', $result['response']['status']);
    }
}
```

### Feature Test

```php
public function test_user_can_make_payment()
{
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/payment/create', [
            'amount' => 10.50,
            'currency' => 'AZN',
        ])
        ->assertRedirect(); // Payment page-ə redirect
}
```

---

## 📊 Log və Monitoring

### Log-ları Yoxlamaq

```php
use App\Repositories\Contracts\EpointLogRepositoryInterface;

public function __construct(
    protected EpointLogRepositoryInterface $logRepo
) {}

public function dashboard()
{
    $stats = $this->logRepo->getStatistics();
    $recentLogs = $this->logRepo->getDashboardData();

    return view('logs.dashboard', compact('stats', 'recentLogs'));
}
```

### Log Filter

```php
$logs = $this->logRepo->getWithFilters([
    'status' => 'failed',
    'date_from' => '2025-01-01',
    'date_to' => '2025-01-31',
], 20);
```

---

## 🔒 Security Best Practices

### 1. Environment Variables

```php
// ❌ Hard-coded keys
$publicKey = 'pub_key_123';

// ✅ Environment-dən
$publicKey = config('services.epoint.public_key');
```

### 2. Rate Limiting

```php
// routes/web.php
Route::middleware(['auth', 'throttle:30,1'])->group(function () {
    Route::post('/payment', [PaymentController::class, 'create']);
});
```

### 3. CSRF Protection

```php
// Blade view
<form method="POST" action="{{ route('payment.create') }}">
    @csrf
    <!-- form fields -->
</form>
```

---

## 📞 Kömək və Dəstək

**Sənədlər:**
- Ətraflı API Docs: `API_DOCUMENTATION.md`
- Refactoring Report: `README_REFACTORING.md`

**Test Environment:**
- Base URL: `https://test.epoint.az/api/1`
- Test Cards: Sənədlərdə mövcuddur

**Postman Collection:**
- `POSTMAN_COLLECTION.json` - İdxal edin və test edin

---

## 🎯 Növbəti Addımlar

1. ✅ `.env` konfiqurasiyasını tamamla
2. ✅ Test kartı ilə ödəniş et
3. ✅ Callback URL-ləri qur
4. ✅ Production keys al
5. ✅ Go live! 🚀

**Uğurlar!** 🎉
