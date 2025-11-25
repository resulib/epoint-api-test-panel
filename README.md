# Epoint Payment Gateway Integration

![Laravel](https://img.shields.io/badge/Laravel-8.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-blue?style=flat-square&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)
![Tests](https://img.shields.io/badge/Tests-Passing-brightgreen?style=flat-square)

**Enterprise-level Epoint Payment Gateway inteqrasiyası - Clean Architecture ilə**

[Sürətli Başlanğıc](#-sürətli-başlanğıc) • [API Docs](API_DOCUMENTATION.md) • [Refactoring Report](README_REFACTORING.md)

---

## ✨ Xüsusiyyətlər

### 💳 Payment Gateway
- ✅ Payment Request, Get Status, Card Registration
- ✅ Execute Pay, Refund, Reverse, Pre-Auth
- ✅ Split Payment, Wallet Management
- ✅ Apple Pay & Google Pay (Widget Token)

### 🧾 Invoice Management
- ✅ Invoice yaratma, yeniləmə, baxış
- ✅ SMS və Email göndərmə
- ✅ Şablon sistemi

### 🏗️ Architecture
- ✅ Repository Pattern, Service Layer
- ✅ DTO (Data Transfer Objects)
- ✅ FormRequest Validation
- ✅ Custom Exception Handling
- ✅ SOLID Principles

### 🔒 Security
- ✅ Rate Limiting, Signature Verification
- ✅ CSRF Protection, Input Validation

---

## 📦 Quraşdırma

```bash
# 1. Layihəni klonlayın
git clone https://github.com/your-repo/epoint-integration.git
cd epoint-integration

# 2. Asılılıqları yükləyin
composer install
npm install

# 3. Environment konfiqurasiyası
cp .env.example .env
php artisan key:generate

# 4. .env faylında Epoint məlumatlarını daxil edin
EPOINT_PUBLIC_KEY=your_public_key_here
EPOINT_PRIVATE_KEY=your_private_key_here

# 5. Database quraşdırması
php artisan migrate

# 6. Serveri işə salın
php artisan serve
```

---

## 🚀 Sürətli Başlanğıc

```php
use App\Services\EpointService;

// Ödəniş sorğusu
$result = $epointService->paymentRequest([
    'amount' => 10.50,
    'currency' => 'AZN',
    'language' => 'az',
    'order_id' => 'ORDER_' . time(),
]);

if ($result['response']['status'] === 'success') {
    return redirect($result['response']['payment_url']);
}
```

**Ətraflı:** [API_QUICK_START.md](API_QUICK_START.md)

---

## 🔗 API Endpoint-ləri

| Endpoint | Method | Təsvir |
|----------|--------|--------|
| `/api/1/request` | POST | Ödəniş sorğusu |
| `/api/1/get-status` | POST | Status yoxlama |
| `/api/1/card-registration` | POST | Kart qeydiyyatı |
| `/api/1/execute-pay` | POST | Saxlanmış kartla ödəniş |
| `/api/1/refund-request` | POST | Geri qaytarma |

**Ətraflı:** [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

---

## 🧪 Testlər

```bash
# Bütün testlər
vendor/bin/phpunit

# Coverage: 60%+
vendor/bin/phpunit --coverage-html coverage
```

---

## 📚 Dokumentasiya

- 📖 [API Documentation](API_DOCUMENTATION.md) - Ətraflı API sənədləri
- 🚀 [Quick Start Guide](API_QUICK_START.md) - 5 dəqiqəlik bələdçi
- 🏗️ [Refactoring Report](README_REFACTORING.md) - Architecture hesabatı
- 📮 [Postman Collection](POSTMAN_COLLECTION.json) - Test collection

---

## 📊 Architecture

```
Presentation → Application → Domain → Infrastructure
(Controllers)  (Services)    (Models)  (Database/APIs)
```

**Folder Strukturu:**
- `app/DTOs/` - Data Transfer Objects
- `app/Repositories/` - Repository Pattern
- `app/Services/` - Business Logic
- `app/Http/Requests/` - FormRequest Validations
- `app/Exceptions/` - Custom Exceptions

---

## 📄 Lisenziya

MIT License

---

Made with ❤️ in Azerbaijan 🇦🇿
