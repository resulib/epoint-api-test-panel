# Epoint Payment Gateway API - İnteqrasiya Dokumentasiyası

## 📚 Mündəricat

1. [Giriş](#giriş)
2. [Autentifikasiya](#autentifikasiya)
3. [Base URL](#base-url)
4. [Request Format](#request-format)
5. [Response Format](#response-format)
6. [API Endpoint-ləri](#api-endpoint-ləri)
   - [Payment APIs](#payment-apis)
   - [Checkout APIs](#checkout-apis)
   - [Invoice APIs](#invoice-apis)
7. [Error Handling](#error-handling)
8. [Rate Limiting](#rate-limiting)
9. [Kod Nümunələri](#kod-nümunələri)

---

## 🚀 Giriş

Epoint Payment Gateway Azərbaycanın aparıcı ödəniş sistemlərindən biridir. Bu dokumentasiya Epoint API-nin Laravel proyektinizə inteqrasiyası üçün bələdçidir.

**API Versiyası:** v1
**Base URL:** `https://epoint.az/api/1`
**Content-Type:** `application/x-www-form-urlencoded`

---

## 🔐 Autentifikasiya

Epoint API istifadə etmək üçün aşağıdakı məlumatlar lazımdır:

```env
EPOINT_PUBLIC_KEY=your_public_key_here
EPOINT_PRIVATE_KEY=your_private_key_here
```

### İmza (Signature) Generasiyası

Hər request üçün signature yaratmaq lazımdır:

```php
// 1. Parametrləri JSON-a çevir və base64 encode et
$data = base64_encode(json_encode($params));

// 2. Signature yaratmaq üçün formula
$signatureString = $privateKey . $data . $privateKey;
$signature = base64_encode(sha1($signatureString, true));
```

### Request Format

```http
POST /api/1/request HTTP/1.1
Host: epoint.az
Content-Type: application/x-www-form-urlencoded

data=eyJhbW91bnQiOjEwLjUsImN1cnJlbmN5IjoiQVpOIn0=&signature=abc123def456...
```

---

## 🌐 Base URL

```
Production: https://epoint.az/api/1
Test: https://test.epoint.az/api/1
```

---

## 📝 Request Format

Bütün POST request-lər aşağıdakı formatda göndərilməlidir:

**Form Data:**
```
data: <base64_encoded_json>
signature: <base64_encoded_sha1>
```

**Nümunə Request Body:**
```php
[
    'data' => 'eyJwdWJsaWNfa2V5IjoieW91cl9wdWJsaWNfa2V5IiwiYW1vdW50Ijo...',
    'signature' => 'YWJjMTIzZGVmNDU2Z2hpNzg5amtsMTIzNDU2Nzg5MA=='
]
```

---

## 📦 Response Format

### Uğurlu Cavab

```json
{
  "status": "success",
  "transaction": "te000000001",
  "message": "Payment successful",
  "order_id": "TEST_123456",
  "amount": 10.50,
  "currency": "AZN"
}
```

### Xəta Cavabı

```json
{
  "status": "error",
  "error": "Invalid amount",
  "code": 422,
  "message": "Məbləğ düzgün deyil"
}
```

---

## 🔗 API Endpoint-ləri

### Payment APIs

#### 1. Payment Request (Ödəniş Sorğusu)

**Endpoint:** `POST /api/1/request`

**Təsvir:** Yeni ödəniş sorğusu yaradır və istifadəçini ödəniş səhifəsinə yönləndirir.

**Request Parametrləri:**

| Parametr | Tip | Tələb | Təsvir | Nümunə |
|----------|-----|-------|--------|--------|
| `public_key` | string | ✅ Bəli | Sizin public açarınız | `pub_key_123` |
| `amount` | float | ✅ Bəli | Ödəniş məbləği (min: 0.01) | `10.50` |
| `currency` | string | ✅ Bəli | Valyuta kodu | `AZN` |
| `language` | string | ✅ Bəli | Dil kodu | `az`, `en`, `ru` |
| `order_id` | string | ✅ Bəli | Unikal sifariş nömrəsi | `TEST_123456` |
| `description` | string | ❌ Xeyr | Ödəniş təsviri | `Test ödənişi` |
| `is_installment` | integer | ❌ Xeyr | Taksit seçimi (0 və ya 1) | `0` |
| `success_redirect_url` | string | ❌ Xeyr | Uğurlu ödənişdən sonra yönləndirmə | `https://example.com/success` |
| `error_redirect_url` | string | ❌ Xeyr | Xəta zamanı yönləndirmə | `https://example.com/error` |

**Request Nümunəsi:**

```php
// Parametrlər
$params = [
    'public_key' => 'your_public_key',
    'amount' => 10.50,
    'currency' => 'AZN',
    'language' => 'az',
    'order_id' => 'TEST_' . time(),
    'description' => 'Test ödənişi',
    'success_redirect_url' => 'https://example.com/payment/success',
    'error_redirect_url' => 'https://example.com/payment/error',
];

// Service istifadəsi
$result = $epointService->paymentRequest($params);
```

**Response Nümunəsi:**

```json
{
  "status": "success",
  "transaction": "te000000001",
  "payment_url": "https://epoint.az/checkout?token=abc123",
  "token": "abc123def456",
  "order_id": "TEST_123456",
  "amount": 10.50,
  "currency": "AZN"
}
```

**HTTP Status Kodları:**
- `200` - Uğurlu
- `400` - Səhv parametrlər
- `401` - Autentifikasiya xətası
- `422` - Validasiya xətası
- `500` - Server xətası

---

#### 2. Get Status (Status Yoxlama)

**Endpoint:** `POST /api/1/get-status`

**Təsvir:** Tranzaksiyanın cari statusunu yoxlayır.

**Request Parametrləri:**

| Parametr | Tip | Tələb | Təsvir | Nümunə |
|----------|-----|-------|--------|--------|
| `public_key` | string | ✅ Bəli | Public açar | `pub_key_123` |
| `transaction` | string | ✅ Bəli | Tranzaksiya ID-si | `te000000001` |

**Request Nümunəsi:**

```php
$result = $epointService->getStatus('te000000001');
```

**Response Nümunəsi:**

```json
{
  "status": "success",
  "transaction": "te000000001",
  "payment_status": "paid",
  "order_id": "TEST_123456",
  "amount": 10.50,
  "currency": "AZN",
  "payment_date": "2025-01-23 15:30:00",
  "card_mask": "************1234"
}
```

**Payment Status Dəyərləri:**
- `new` - Yeni ödəniş
- `pending` - Gözləmədə
- `paid` - Ödənilib
- `failed` - Uğursuz
- `cancelled` - Ləğv edilib
- `refunded` - Geri qaytarılıb

---

#### 3. Card Registration (Kart Qeydiyyatı)

**Endpoint:** `POST /api/1/card-registration`

**Təsvir:** İstifadəçinin kartını qeydiyyatdan keçirir (tokenization).

**Request Parametrləri:**

| Parametr | Tip | Tələb | Təsvir | Nümunə |
|----------|-----|-------|--------|--------|
| `public_key` | string | ✅ Bəli | Public açar | `pub_key_123` |
| `language` | string | ✅ Bəli | Dil kodu | `az` |
| `refund` | integer | ❌ Xeyr | Geri qaytarma üçün (0 və ya 1) | `0` |
| `description` | string | ❌ Xeyr | Təsvir | `Kart qeydiyyatı` |

**Request Nümunəsi:**

```php
$params = [
    'language' => 'az',
    'description' => 'Kart qeydiyyatı',
];

$result = $epointService->cardRegistration($params);
```

**Response Nümunəsi:**

```json
{
  "status": "success",
  "card_id": "card_123456",
  "registration_url": "https://epoint.az/card-registration?token=abc123",
  "token": "abc123def456"
}
```

---

#### 4. Execute Payment (Saxlanmış Kartla Ödəniş)

**Endpoint:** `POST /api/1/execute-pay`

**Təsvir:** Əvvəlcədən saxlanmış kartla ödəniş aparır.

**Request Parametrləri:**

| Parametr | Tip | Tələb | Təsvir | Nümunə |
|----------|-----|-------|--------|--------|
| `public_key` | string | ✅ Bəli | Public açar | `pub_key_123` |
| `language` | string | ✅ Bəli | Dil kodu | `az` |
| `card_id` | string | ✅ Bəli | Saxlanmış kart ID-si | `card_123456` |
| `order_id` | string | ✅ Bəli | Sifariş nömrəsi | `TEST_123456` |
| `amount` | float | ✅ Bəli | Məbləğ | `10.50` |
| `currency` | string | ✅ Bəli | Valyuta | `AZN` |
| `description` | string | ❌ Xeyr | Təsvir | `Ödəniş` |

**Request Nümunəsi:**

```php
$params = [
    'language' => 'az',
    'card_id' => 'card_123456',
    'order_id' => 'TEST_' . time(),
    'amount' => 10.50,
    'currency' => 'AZN',
    'description' => 'Saxlanmış kartla ödəniş',
];

$result = $epointService->executePay($params);
```

**Response Nümunəsi:**

```json
{
  "status": "success",
  "transaction": "te000000002",
  "payment_status": "paid",
  "order_id": "TEST_123456",
  "amount": 10.50,
  "currency": "AZN"
}
```

---

#### 5. Refund Request (Geri Qaytarma)

**Endpoint:** `POST /api/1/refund-request`

**Təsvir:** Ödənişi geri qaytarır.

**Request Parametrləri:**

| Parametr | Tip | Tələb | Təsvir | Nümunə |
|----------|-----|-------|--------|--------|
| `public_key` | string | ✅ Bəli | Public açar | `pub_key_123` |
| `language` | string | ✅ Bəli | Dil kodu | `az` |
| `card_id` | string | ✅ Bəli | Kart ID-si | `card_123456` |
| `order_id` | string | ✅ Bəli | Sifariş nömrəsi | `REFUND_123456` |
| `amount` | float | ✅ Bəli | Məbləğ | `10.50` |
| `currency` | string | ✅ Bəli | Valyuta | `AZN` |
| `description` | string | ❌ Xeyr | Təsvir | `Geri qaytarma` |

**Request Nümunəsi:**

```php
$params = [
    'language' => 'az',
    'card_id' => 'card_123456',
    'order_id' => 'REFUND_' . time(),
    'amount' => 10.50,
    'currency' => 'AZN',
];

$result = $epointService->refundRequest($params);
```

**Response Nümunəsi:**

```json
{
  "status": "success",
  "transaction": "te000000003",
  "refund_status": "completed",
  "order_id": "REFUND_123456",
  "amount": 10.50,
  "currency": "AZN"
}
```

---

#### 6. Reverse Transaction (Tranzaksiyanı Ləğv Et)

**Endpoint:** `POST /api/1/reverse`

**Təsvir:** Tranzaksiyanı ləğv edir (tam və ya qismən).

**Request Parametrləri:**

| Parametr | Tip | Tələb | Təsvir | Nümunə |
|----------|-----|-------|--------|--------|
| `public_key` | string | ✅ Bəli | Public açar | `pub_key_123` |
| `language` | string | ✅ Bəli | Dil kodu | `az` |
| `transaction` | string | ✅ Bəli | Tranzaksiya ID-si | `te000000001` |
| `amount` | float | ❌ Xeyr | Məbləğ (qismən üçün) | `5.00` |
| `currency` | string | ✅ Bəli | Valyuta | `AZN` |

**Request Nümunəsi:**

```php
$params = [
    'language' => 'az',
    'transaction' => 'te000000001',
    'amount' => 10.50,
    'currency' => 'AZN',
];

$result = $epointService->reverse($params);
```

---

#### 7. Pre-Auth Request (Pre-Autorizasiya)

**Endpoint:** `POST /api/1/pre-auth-request`

**Təsvir:** Məbləği bloklamaq üçün (məsələn, otel rezervasiyaları).

**Request Parametrləri:**

| Parametr | Tip | Tələb | Təsvir |
|----------|-----|-------|--------|
| `amount` | float | ✅ Bəli | Məbləğ |
| `currency` | string | ✅ Bəli | Valyuta |
| `language` | string | ✅ Bəli | Dil |
| `order_id` | string | ✅ Bəli | Sifariş ID |

**Response Nümunəsi:**

```json
{
  "status": "success",
  "transaction": "te000000004",
  "pre_auth_status": "blocked",
  "amount": 50.00
}
```

---

#### 8. Pre-Auth Complete (Pre-Autorizasiyanı Tamamla)

**Endpoint:** `POST /api/1/pre-auth-complete`

**Təsvir:** Bloklanan məbləği tutur.

**Request Parametrləri:**

| Parametr | Tip | Tələb | Təsvir |
|----------|-----|-------|--------|
| `amount` | float | ✅ Bəli | Tutulacaq məbləğ |
| `transaction` | string | ✅ Bəli | Pre-auth tranzaksiya ID |

---

### Checkout APIs

#### 9. Checkout Request

**Endpoint:** `POST /api/1/checkout`

**Təsvir:** Checkout səhifəsinə yönləndirmək üçün token alır.

Parametrlər Payment Request ilə eynidir, lakin daha sadələşdirilmiş checkout flow təmin edir.

---

### Invoice APIs

#### 10. Create Invoice (Faktura Yarat)

**Endpoint:** `POST /api/1/invoices/create`

**Təsvir:** Yeni faktura yaradır və müştəriyə göndərilə bilər.

**Request Parametrləri:**

| Parametr | Tip | Tələb | Təsvir | Nümunə |
|----------|-----|-------|--------|--------|
| `public_key` | string | ✅ Bəli | Public açar | `pub_key_123` |
| `sum` | float | ✅ Bəli | Məbləğ | `100.50` |
| `display` | integer | ✅ Bəli | Göstərmək (0/1) | `1` |
| `save_as_template` | integer | ✅ Bəli | Şablon kimi saxla (0/1) | `0` |
| `name` | string | ❌ Xeyr | Müştəri adı | `Əli Məmmədov` |
| `phone` | string | ❌ Xeyr | Telefon | `+994501234567` |
| `email` | string | ❌ Xeyr | Email | `test@example.com` |
| `inn` | string | ❌ Xeyr | VÖEN | `1234567890` |
| `contract_number` | string | ❌ Xeyr | Müqavilə nömrəsi | `CONTRACT_123` |
| `merchant_order_id` | string | ❌ Xeyr | Sifariş ID | `ORDER_123` |
| `description` | string | ❌ Xeyr | Təsvir | `Xidmət haqqı` |
| `period_from` | date | ❌ Xeyr | Dövr başlanğıcı | `2025-01-01` |
| `period_to` | date | ❌ Xeyr | Dövr sonu | `2025-01-31` |
| `invoice_images` | file | ❌ Xeyr | Faktura şəkilləri | - |

**Request Nümunəsi:**

```php
$params = [
    'sum' => 100.50,
    'display' => 1,
    'save_as_template' => 0,
    'name' => 'Əli Məmmədov',
    'phone' => '+994501234567',
    'email' => 'ali@example.com',
    'description' => 'Xidmət haqqı',
];

$result = $epointService->invoiceCreate($params);
```

**Response Nümunəsi:**

```json
{
  "status": "success",
  "invoice_id": 12345,
  "invoice_number": "INV-2025-001",
  "sum": 100.50,
  "invoice_url": "https://epoint.az/invoices/view/12345",
  "payment_link": "https://epoint.az/pay/abc123"
}
```

---

#### 11. Update Invoice (Faktura Yenilə)

**Endpoint:** `POST /api/1/invoices/update`

**Parametrlər:** Create Invoice ilə eyni + `id` parametri

---

#### 12. View Invoice (Faktura Baxış)

**Endpoint:** `POST /api/1/invoices/view`

**Request Parametrləri:**

| Parametr | Tip | Tələb |
|----------|-----|-------|
| `id` | integer | ✅ Bəli |

---

#### 13. List Invoices (Faktura Siyahısı)

**Endpoint:** `POST /api/1/invoices/list`

**Response Nümunəsi:**

```json
{
  "status": "success",
  "invoices": [
    {
      "id": 12345,
      "invoice_number": "INV-2025-001",
      "sum": 100.50,
      "status": "paid",
      "created_at": "2025-01-23 10:00:00"
    }
  ],
  "total": 10
}
```

---

#### 14. Send Invoice via SMS

**Endpoint:** `POST /api/1/invoices/send-sms`

**Request Parametrləri:**

| Parametr | Tip | Tələb |
|----------|-----|-------|
| `id` | integer | ✅ Bəli |
| `phone` | string | ✅ Bəli |

---

#### 15. Send Invoice via Email

**Endpoint:** `POST /api/1/invoices/send-email`

**Request Parametrləri:**

| Parametr | Tip | Tələb |
|----------|-----|-------|
| `id` | integer | ✅ Bəli |
| `email` | string | ✅ Bəli |

---

### Split Payment APIs

#### 16. Split Payment Request

**Endpoint:** `POST /api/1/split-request`

**Təsvir:** Ödənişi bir neçə tərəf arasında bölüşdürmək üçün.

**Request Parametrləri:**

| Parametr | Tip | Tələb | Təsvir |
|----------|-----|-------|--------|
| `amount` | float | ✅ Bəli | Ümumi məbləğ |
| `split_user` | string | ✅ Bəli | Split user ID |
| `split_amount` | float | ✅ Bəli | Split məbləği |
| `wallet_id` | string | ❌ Xeyr | Wallet ID |

---

### Wallet APIs

#### 17. Wallet Status

**Endpoint:** `POST /api/1/wallet/status`

**Təsvir:** Wallet statusunu və balansı yoxlayır.

**Response Nümunəsi:**

```json
{
  "status": "success",
  "wallet_id": "wallet_123",
  "balance": 150.75,
  "currency": "AZN",
  "is_active": true
}
```

---

#### 18. Wallet Payment

**Endpoint:** `POST /api/1/wallet/payment`

**Təsvir:** Wallet-dən ödəniş aparır.

---

### Widget Token (Apple/Google Pay)

#### 19. Widget Token

**Endpoint:** `POST /api/1/token/widget`

**Təsvir:** Apple Pay və Google Pay üçün token yaradır.

**Request Parametrləri:**

| Parametr | Tip | Tələb |
|----------|-----|-------|
| `amount` | float | ✅ Bəli |
| `order_id` | string | ✅ Bəli |
| `description` | string | ✅ Bəli |

---

## ⚠️ Error Handling

### Error Kodları

| HTTP Status | Kod | Mənası | Həll |
|-------------|-----|--------|------|
| 400 | `BAD_REQUEST` | Səhv parametrlər | Parametrləri yoxlayın |
| 401 | `UNAUTHORIZED` | İmza səhvdir | Public/Private key yoxlayın |
| 404 | `NOT_FOUND` | Endpoint tapılmadı | URL yoxlayın |
| 422 | `VALIDATION_ERROR` | Validasiya xətası | Parametr formatını yoxlayın |
| 429 | `TOO_MANY_REQUESTS` | Rate limit aşılıb | Bir az gözləyin |
| 500 | `INTERNAL_ERROR` | Server xətası | Dəstəklə əlaqə saxlayın |

### Error Response Nümunəsi

```json
{
  "status": "error",
  "error": "Invalid signature",
  "code": 401,
  "message": "İmza doğrulanması uğursuz oldu",
  "details": {
    "field": "signature",
    "reason": "Signature verification failed"
  }
}
```

---

## 🚦 Rate Limiting

| Endpoint Tipi | Limit | Müddət |
|---------------|-------|--------|
| Login | 10 request | 1 dəqiqə |
| Standard API | 60 request | 1 dəqiqə |
| Payment API | 30 request | 1 dəqiqə |

**Rate Limit Cavabı:**

```json
{
  "error": "Too many requests",
  "message": "Rate limit exceeded. Please try again later.",
  "retry_after": 60
}
```

---

## 💻 Kod Nümunələri

### PHP (Laravel) - EpointService İstifadəsi

```php
use App\Services\EpointService;
use App\DTOs\PaymentRequestDTO;

// 1. Payment Request
$dto = PaymentRequestDTO::fromArray([
    'amount' => 10.50,
    'currency' => 'AZN',
    'language' => 'az',
    'order_id' => 'TEST_' . time(),
    'description' => 'Test ödənişi',
]);

$result = $epointService->paymentRequest($dto->toArray());

if ($result['response']['status'] === 'success') {
    // Redirect to payment page
    $paymentUrl = $result['response']['payment_url'];
    return redirect($paymentUrl);
}

// 2. Check Payment Status
$status = $epointService->getStatus('te000000001');

if ($status['response']['payment_status'] === 'paid') {
    // Payment successful
    echo "Ödəniş uğurla tamamlandı!";
}

// 3. Refund
$refund = $epointService->refundRequest([
    'language' => 'az',
    'card_id' => 'card_123456',
    'order_id' => 'REFUND_' . time(),
    'amount' => 10.50,
    'currency' => 'AZN',
]);
```

### cURL Nümunəsi

```bash
# Payment Request
curl -X POST https://epoint.az/api/1/request \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "data=eyJwdWJsaWNfa2V5Ijoi..." \
  -d "signature=YWJjMTIzZGVm..."

# Get Status
curl -X POST https://epoint.az/api/1/get-status \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "data=eyJ0cmFuc2FjdGlvbiI6..." \
  -d "signature=ZGVmNDU2Z2hp..."
```

### JavaScript (Axios)

```javascript
// Payment Request
const params = {
  public_key: 'your_public_key',
  amount: 10.50,
  currency: 'AZN',
  language: 'az',
  order_id: 'TEST_' + Date.now(),
};

// Encode and sign
const data = btoa(JSON.stringify(params));
const signature = generateSignature(data); // Your signature function

axios.post('https://epoint.az/api/1/request', {
  data: data,
  signature: signature
}, {
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded'
  }
})
.then(response => {
  if (response.data.status === 'success') {
    window.location.href = response.data.payment_url;
  }
});
```

---

## 🔧 Testing

### Test Credentials

```
Test Public Key: test_public_key_12345
Test Private Key: test_private_key_67890
Test Base URL: https://test.epoint.az/api/1
```

### Test Cards

| Kart Nömrəsi | Nəticə | CVV | Exp |
|-------------|--------|-----|-----|
| 4169738225000008 | Uğurlu | 123 | 12/25 |
| 5108757373222250 | Xəta | 456 | 06/26 |

---

## 📞 Dəstək

**Texniki Dəstək:**
Email: support@epoint.az
Telefon: +994 12 XXX XX XX
Sənədlər: https://epoint.az/docs

**İş Saatları:**
Bazar ertəsi - Cümə: 09:00 - 18:00 (GMT+4)

---

## 📋 Changelog

### v2.0.0 (2025-01-23)
- Repository Pattern əlavə olundu
- DTO class-ları yaradıldı
- FormRequest validasiyaları
- Custom Exception handling
- Rate limiting tətbiq olundu

### v1.0.0 (Initial Release)
- Basic payment integration
- Service layer implementation

---

**Son Yeniləmə:** 2025-01-23
**Versiya:** 2.0.0
**Müəllif:** Epoint Integration Team
