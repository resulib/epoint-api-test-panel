@extends('layouts.app')

@section('title', 'Epoint API Documentation')

@section('content')
<div class="docs-container">
    <div class="docs-sidebar">
        <div class="sidebar-header">
            <h3>📚 Dokumentasiya</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#introduction">Giriş</a></li>
            <li><a href="#authentication">Autentifikasiya</a></li>
            <li><a href="#base-url">Base URL</a></li>
            <li><a href="#request-format">Request Format</a></li>
            <li><a href="#response-format">Response Format</a></li>
            <li class="menu-header">Payment APIs</li>
            <li><a href="#payment-request">1. Payment Request</a></li>
            <li><a href="#get-status">2. Get Status</a></li>
            <li><a href="#card-registration">3. Card Registration</a></li>
            <li><a href="#execute-pay">4. Execute Payment</a></li>
            <li><a href="#refund">5. Refund Request</a></li>
            <li><a href="#reverse">6. Reverse Transaction</a></li>
            <li><a href="#pre-auth">7. Pre-Auth</a></li>
            <li class="menu-header">Split Payment APIs</li>
            <li><a href="#split-request">Split Payment Request</a></li>
            <li><a href="#split-execute">Split Execute Pay</a></li>
            <li class="menu-header">Wallet APIs</li>
            <li><a href="#wallet-status">Wallet Status</a></li>
            <li><a href="#wallet-payment">Wallet Payment</a></li>
            <li class="menu-header">Widget & Checkout</li>
            <li><a href="#widget-token">Widget Token (Apple/Google Pay)</a></li>
            <li><a href="#checkout-request">Checkout Request</a></li>
            <li class="menu-header">Invoice APIs</li>
            <li><a href="#invoice-create">Create Invoice</a></li>
            <li><a href="#invoice-update">Update Invoice</a></li>
            <li><a href="#invoice-view">View Invoice</a></li>
            <li><a href="#invoice-list">List Invoices</a></li>
            <li><a href="#invoice-sms">Send Invoice via SMS</a></li>
            <li><a href="#invoice-email">Send Invoice via Email</a></li>
            <li class="menu-header">Digər</li>
            <li><a href="#error-handling">Error Handling</a></li>
            <li><a href="#rate-limiting">Rate Limiting</a></li>
            <li><a href="#examples">Kod Nümunələri</a></li>
        </ul>
    </div>

    <div class="docs-content">
        <!-- Header -->
        <div class="docs-header">
            <h1>🚀 Epoint Payment Gateway API</h1>
            <p class="lead">Azərbaycanın aparıcı ödəniş sisteminin API inteqrasiya dokumentasiyası</p>
            <div class="badge-group">
                <span class="badge badge-version">v2.0.0</span>
                <span class="badge badge-status">Production Ready</span>
            </div>
        </div>

        <!-- Introduction -->
        <section id="introduction" class="doc-section">
            <h2>📖 Giriş</h2>
            <p>Epoint Payment Gateway Azərbaycanın aparıcı ödəniş sistemlərindən biridir. Bu dokumentasiya Epoint API-nin Laravel proyektinizə inteqrasiyası üçün bələdçidir.</p>

            <div class="info-box info">
                <strong>API Versiyası:</strong> v1<br>
                <strong>Base URL:</strong> <code>https://epoint.az/api/1</code><br>
                <strong>Content-Type:</strong> <code>application/x-www-form-urlencoded</code>
            </div>

            <div class="quick-links">
                <a href="{{ route('docs.quick-start') }}" class="btn btn-primary">🚀 Sürətli Başlanğıc</a>
                <a href="{{ asset('POSTMAN_COLLECTION.json') }}" download class="btn btn-secondary">📮 Postman Collection</a>
            </div>
        </section>

        <!-- Authentication -->
        <section id="authentication" class="doc-section">
            <h2>🔐 Autentifikasiya</h2>
            <p>Epoint API istifadə etmək üçün aşağıdakı məlumatlar lazımdır:</p>

            <div class="code-block">
                <div class="code-header">
                    <span>.env</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code>EPOINT_PUBLIC_KEY=your_public_key_here
EPOINT_PRIVATE_KEY=your_private_key_here</code></pre>
            </div>

            <h3>İmza (Signature) Generasiyası</h3>
            <p>Hər request üçün signature yaratmaq lazımdır:</p>

            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">// 1. Parametrləri JSON-a çevir və base64 encode et
$data = base64_encode(json_encode($params));

// 2. Signature yaratmaq üçün formula
$signatureString = $privateKey . $data . $privateKey;
$signature = base64_encode(sha1($signatureString, true));</code></pre>
            </div>
        </section>

        <!-- Base URL -->
        <section id="base-url" class="doc-section">
            <h2>🌐 Base URL</h2>
            <div class="url-table">
                <table>
                    <tr>
                        <td><strong>Production:</strong></td>
                        <td><code>https://epoint.az/api/1</code></td>
                    </tr>
                    <tr>
                        <td><strong>Test:</strong></td>
                        <td><code>https://test.epoint.az/api/1</code></td>
                    </tr>
                </table>
            </div>
        </section>

        <!-- Request Format -->
        <section id="request-format" class="doc-section">
            <h2>📝 Request Format</h2>
            <p>Bütün POST request-lər aşağıdakı formatda göndərilməlidir:</p>

            <div class="info-box warning">
                <strong>⚠️ Diqqət:</strong> Request body <code>application/x-www-form-urlencoded</code> formatında olmalıdır.
            </div>

            <div class="code-block">
                <div class="code-header">
                    <span>Request Format</span>
                </div>
                <pre><code>data: &lt;base64_encoded_json&gt;
signature: &lt;base64_encoded_sha1&gt;</code></pre>
            </div>
        </section>

        <!-- Response Format -->
        <section id="response-format" class="doc-section">
            <h2>📦 Response Format</h2>

            <h3>✅ Uğurlu Cavab</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>JSON Response</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-json">{
  "status": "success",
  "transaction": "te000000001",
  "message": "Payment successful",
  "order_id": "TEST_123456",
  "amount": 10.50,
  "currency": "AZN"
}</code></pre>
            </div>

            <h3>❌ Xəta Cavabı</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>JSON Response</span>
                </div>
                <pre><code class="language-json">{
  "status": "error",
  "error": "Invalid amount",
  "code": 422,
  "message": "Məbləğ düzgün deyil"
}</code></pre>
            </div>
        </section>

        <!-- Payment Request -->
        <section id="payment-request" class="doc-section">
            <h2>💳 1. Payment Request</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/request</span>
            </div>

            <p>Yeni ödəniş sorğusu yaradır və istifadəçini ödəniş səhifəsinə yönləndirir.</p>

            <h3>Request Parametrləri</h3>
            <div class="param-table">
                <table>
                    <thead>
                        <tr>
                            <th>Parametr</th>
                            <th>Tip</th>
                            <th>Tələb</th>
                            <th>Təsvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>amount</code></td>
                            <td>float</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Ödəniş məbləği (min: 0.01)</td>
                        </tr>
                        <tr>
                            <td><code>currency</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Valyuta kodu (AZN)</td>
                        </tr>
                        <tr>
                            <td><code>language</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Dil kodu (az, en, ru)</td>
                        </tr>
                        <tr>
                            <td><code>order_id</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Unikal sifariş nömrəsi</td>
                        </tr>
                        <tr>
                            <td><code>description</code></td>
                            <td>string</td>
                            <td><span class="badge badge-optional">Optional</span></td>
                            <td>Ödəniş təsviri</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3>Kod Nümunəsi</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">use App\Services\EpointService;

$result = $epointService->paymentRequest([
    'amount' => 10.50,
    'currency' => 'AZN',
    'language' => 'az',
    'order_id' => 'ORDER_' . time(),
    'description' => 'Test ödənişi',
]);

if ($result['response']['status'] === 'success') {
    return redirect($result['response']['payment_url']);
}</code></pre>
            </div>

            <h3>Response Nümunəsi</h3>
            <div class="code-block">
                <pre><code class="language-json">{
  "status": "success",
  "transaction": "te000000001",
  "payment_url": "https://epoint.az/checkout?token=abc123",
  "token": "abc123def456",
  "order_id": "TEST_123456",
  "amount": 10.50,
  "currency": "AZN"
}</code></pre>
            </div>
        </section>

        <!-- Get Status -->
        <section id="get-status" class="doc-section">
            <h2>🔍 2. Get Status</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/get-status</span>
            </div>

            <p>Tranzaksiyanın cari statusunu yoxlayır.</p>

            <h3>Request Parametrləri</h3>
            <div class="param-table">
                <table>
                    <thead>
                        <tr>
                            <th>Parametr</th>
                            <th>Tip</th>
                            <th>Tələb</th>
                            <th>Təsvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>transaction</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Tranzaksiya ID-si</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3>Payment Status Dəyərləri</h3>
            <div class="status-list">
                <div class="status-item">
                    <span class="status-badge new">new</span>
                    <span>Yeni ödəniş</span>
                </div>
                <div class="status-item">
                    <span class="status-badge pending">pending</span>
                    <span>Gözləmədə</span>
                </div>
                <div class="status-item">
                    <span class="status-badge paid">paid</span>
                    <span>Ödənilib</span>
                </div>
                <div class="status-item">
                    <span class="status-badge failed">failed</span>
                    <span>Uğursuz</span>
                </div>
            </div>

            <h3>Kod Nümunəsi</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->getStatus('te000000001');

if ($result['response']['payment_status'] === 'paid') {
    // Ödəniş uğurlu
    echo "Ödəniş tamamlandı!";
}</code></pre>
            </div>
        </section>

        <!-- Card Registration -->
        <section id="card-registration" class="doc-section">
            <h2>💳 3. Card Registration</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/card-registration</span>
            </div>

            <p>İstifadəçinin kartını qeydiyyatdan keçirir (tokenization) - gələcək ödənişlər üçün saxlamaq.</p>

            <h3>Request Parametrləri</h3>
            <div class="param-table">
                <table>
                    <thead>
                        <tr>
                            <th>Parametr</th>
                            <th>Tip</th>
                            <th>Tələb</th>
                            <th>Təsvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>language</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Dil kodu (az, en, ru)</td>
                        </tr>
                        <tr>
                            <td><code>refund</code></td>
                            <td>integer</td>
                            <td><span class="badge badge-optional">Optional</span></td>
                            <td>Geri qaytarma üçün (0 və ya 1)</td>
                        </tr>
                        <tr>
                            <td><code>description</code></td>
                            <td>string</td>
                            <td><span class="badge badge-optional">Optional</span></td>
                            <td>Təsvir</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3>Kod Nümunəsi</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->cardRegistration([
    'language' => 'az',
    'description' => 'Kart qeydiyyatı'
]);

// İstifadəçini kart qeydiyyat səhifəsinə yönləndir
return redirect($result['response']['registration_url']);</code></pre>
            </div>
        </section>

        <!-- Execute Pay -->
        <section id="execute-pay" class="doc-section">
            <h2>⚡ 4. Execute Payment (Saxlanmış Kartla)</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/execute-pay</span>
            </div>

            <p>Əvvəlcədən saxlanmış kart ilə ödəniş aparır. Kart məlumatlarını yenidən daxil etmədən ödəniş.</p>

            <h3>Request Parametrləri</h3>
            <div class="param-table">
                <table>
                    <thead>
                        <tr>
                            <th>Parametr</th>
                            <th>Tip</th>
                            <th>Tələb</th>
                            <th>Təsvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>card_id</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Saxlanmış kart ID-si</td>
                        </tr>
                        <tr>
                            <td><code>order_id</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Unikal sifariş nömrəsi</td>
                        </tr>
                        <tr>
                            <td><code>amount</code></td>
                            <td>float</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Ödəniş məbləği</td>
                        </tr>
                        <tr>
                            <td><code>currency</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Valyuta (AZN)</td>
                        </tr>
                        <tr>
                            <td><code>language</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Dil kodu</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3>Kod Nümunəsi</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->executePay([
    'language' => 'az',
    'card_id' => 'card_123456',
    'order_id' => 'ORDER_' . time(),
    'amount' => 25.00,
    'currency' => 'AZN',
    'description' => 'Saxlanmış kartla ödəniş'
]);</code></pre>
            </div>
        </section>

        <!-- Refund -->
        <section id="refund" class="doc-section">
            <h2>↩️ 5. Refund Request</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/refund-request</span>
            </div>

            <p>Ödənişi geri qaytarır (tam və ya qismən).</p>

            <h3>Request Parametrləri</h3>
            <div class="param-table">
                <table>
                    <thead>
                        <tr>
                            <th>Parametr</th>
                            <th>Tip</th>
                            <th>Tələb</th>
                            <th>Təsvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>card_id</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Kart ID-si</td>
                        </tr>
                        <tr>
                            <td><code>order_id</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Geri qaytarma ID-si</td>
                        </tr>
                        <tr>
                            <td><code>amount</code></td>
                            <td>float</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Geri qaytarılacaq məbləğ</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3>Kod Nümunəsi</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->refundRequest([
    'language' => 'az',
    'card_id' => 'card_123456',
    'order_id' => 'REFUND_' . time(),
    'amount' => 10.50,
    'currency' => 'AZN'
]);</code></pre>
            </div>
        </section>

        <!-- Reverse -->
        <section id="reverse" class="doc-section">
            <h2>🔄 6. Reverse Transaction</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/reverse</span>
            </div>

            <p>Tranzaksiyanı ləğv edir (tam və ya qismən). Ödənişdən dərhal sonra istifadə olunur.</p>

            <h3>Request Parametrləri</h3>
            <div class="param-table">
                <table>
                    <thead>
                        <tr>
                            <th>Parametr</th>
                            <th>Tip</th>
                            <th>Tələb</th>
                            <th>Təsvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>transaction</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Tranzaksiya ID-si</td>
                        </tr>
                        <tr>
                            <td><code>amount</code></td>
                            <td>float</td>
                            <td><span class="badge badge-optional">Optional</span></td>
                            <td>Qismən ləğv üçün məbləğ</td>
                        </tr>
                        <tr>
                            <td><code>language</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Dil kodu</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Pre-Auth -->
        <section id="pre-auth" class="doc-section">
            <h2>🔒 7. Pre-Authorization</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/pre-auth-request</span>
            </div>

            <p>Məbləği bloklamaq üçün (məsələn, otel rezervasiyaları). Sonra <code>/pre-auth-complete</code> ilə tutulur.</p>

            <h3>İstifadə Halları</h3>
            <div class="info-box info">
                <strong>📌 Nümunələr:</strong><br>
                • Otel rezervasiyası - check-in zamanı depozit<br>
                • Avtomobil icarəsi - zərər depositi<br>
                • Event biletləri - early bird saxlama
            </div>

            <h3>Request Parametrləri</h3>
            <div class="param-table">
                <table>
                    <thead>
                        <tr>
                            <th>Parametr</th>
                            <th>Tip</th>
                            <th>Tələb</th>
                            <th>Təsvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>amount</code></td>
                            <td>float</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Bloklanacaq məbləğ</td>
                        </tr>
                        <tr>
                            <td><code>currency</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Valyuta</td>
                        </tr>
                        <tr>
                            <td><code>order_id</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Sifariş ID</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3>Pre-Auth Complete</h3>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/pre-auth-complete</span>
            </div>
            <p>Bloklanan məbləği tutur və ödənişi tamamlayır.</p>

            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">// 1. Pre-auth request
$preAuth = $epointService->preAuthRequest([
    'amount' => 50.00,
    'currency' => 'AZN',
    'order_id' => 'PREAUTH_' . time(),
    'language' => 'az'
]);

// 2. Sonra complete et
$complete = $epointService->preAuthComplete([
    'amount' => 50.00,
    'transaction' => $preAuth['response']['transaction']
]);</code></pre>
            </div>
        </section>

        <!-- Split Payment Request -->
        <section id="split-request" class="doc-section">
            <h2>🔀 Split Payment Request</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/split-request</span>
            </div>

            <p>Ödənişi bir neçə tərəf arasında bölüşdürmək üçün (marketplace, komissiya paylaşımı).</p>

            <h3>İstifadə Halları</h3>
            <div class="info-box info">
                <strong>💡 Nümunələr:</strong><br>
                • Marketplace - satıcı və platform komissiyas ı<br>
                • Affiliate komissiyalar<br>
                • Multi-vendor platformalar
            </div>

            <h3>Request Parametrləri</h3>
            <div class="param-table">
                <table>
                    <thead>
                        <tr>
                            <th>Parametr</th>
                            <th>Tip</th>
                            <th>Tələb</th>
                            <th>Təsvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>amount</code></td>
                            <td>float</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Ümumi məbləğ</td>
                        </tr>
                        <tr>
                            <td><code>split_user</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Split user ID</td>
                        </tr>
                        <tr>
                            <td><code>split_amount</code></td>
                            <td>float</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Split məbləği</td>
                        </tr>
                        <tr>
                            <td><code>wallet_id</code></td>
                            <td>string</td>
                            <td><span class="badge badge-optional">Optional</span></td>
                            <td>Wallet ID</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3>Kod Nümunəsi</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->splitRequest([
    'amount' => 100.00,           // Ümumi məbləğ
    'split_user' => 'i000000002', // Partner ID
    'split_amount' => 15.00,      // Partnerin payı (15%)
    'currency' => 'AZN',
    'language' => 'az',
    'order_id' => 'SPLIT_' . time()
]);</code></pre>
            </div>
        </section>

        <!-- Split Execute Pay -->
        <section id="split-execute" class="doc-section">
            <h2>⚡ Split Execute Pay</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/split-execute-pay</span>
            </div>

            <p>Saxlanmış kart ilə split payment aparır.</p>

            <h3>Request Parametrləri</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->splitExecutePay([
    'card_id' => 'card_123456',
    'order_id' => 'SPLIT_' . time(),
    'amount' => 100.00,
    'split_user' => 'i000000002',
    'split_amount' => 15.00,
    'currency' => 'AZN',
    'language' => 'az'
]);</code></pre>
            </div>
        </section>

        <!-- Wallet Status -->
        <section id="wallet-status" class="doc-section">
            <h2>👛 Wallet Status</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/wallet/status</span>
            </div>

            <p>Wallet statusunu və balansı yoxlayır.</p>

            <h3>Response Nümunəsi</h3>
            <div class="code-block">
                <pre><code class="language-json">{
  "status": "success",
  "wallet_id": "wallet_123",
  "balance": 150.75,
  "currency": "AZN",
  "is_active": true
}</code></pre>
            </div>

            <h3>Kod Nümunəsi</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->walletStatus();

$balance = $result['response']['balance'];
echo "Wallet balansı: {$balance} AZN";</code></pre>
            </div>
        </section>

        <!-- Wallet Payment -->
        <section id="wallet-payment" class="doc-section">
            <h2>💰 Wallet Payment</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/wallet/payment</span>
            </div>

            <p>Wallet-dən ödəniş aparır.</p>

            <h3>Request Parametrləri</h3>
            <div class="param-table">
                <table>
                    <thead>
                        <tr>
                            <th>Parametr</th>
                            <th>Tip</th>
                            <th>Tələb</th>
                            <th>Təsvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>wallet_id</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Wallet ID</td>
                        </tr>
                        <tr>
                            <td><code>amount</code></td>
                            <td>float</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Ödəniş məbləği</td>
                        </tr>
                        <tr>
                            <td><code>order_id</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Sifariş ID</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3>Kod Nümunəsi</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->walletPayment([
    'wallet_id' => 'wallet_123',
    'amount' => 25.00,
    'currency' => 'AZN',
    'order_id' => 'WALLET_' . time(),
    'language' => 'az'
]);</code></pre>
            </div>
        </section>

        <!-- Widget Token -->
        <section id="widget-token" class="doc-section">
            <h2>📱 Widget Token (Apple/Google Pay)</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/token/widget</span>
            </div>

            <p>Apple Pay və Google Pay üçün widget token yaradır.</p>

            <div class="info-box info">
                <strong>🍎 🤖 Apple Pay & Google Pay</strong><br>
                Bu endpoint mobil ödənişlər üçün istifadə olunur. Widget token alıb Apple Pay və ya Google Pay button-a inteqrasiya edə bilərsiniz.
            </div>

            <h3>Request Parametrləri</h3>
            <div class="param-table">
                <table>
                    <thead>
                        <tr>
                            <th>Parametr</th>
                            <th>Tip</th>
                            <th>Tələb</th>
                            <th>Təsvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>amount</code></td>
                            <td>float</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Ödəniş məbləği</td>
                        </tr>
                        <tr>
                            <td><code>order_id</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Sifariş ID</td>
                        </tr>
                        <tr>
                            <td><code>description</code></td>
                            <td>string</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Ödəniş təsviri</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3>Kod Nümunəsi</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->createWidgetToken([
    'amount' => 50.00,
    'order_id' => 'WIDGET_' . time(),
    'description' => 'Apple Pay ödənişi'
]);

$widgetToken = $result['response']['token'];

// Frontend-də Apple Pay button-a əlavə et
return view('checkout', ['widgetToken' => $widgetToken]);</code></pre>
            </div>

            <h3>Response Nümunəsi</h3>
            <div class="code-block">
                <pre><code class="language-json">{
  "status": "success",
  "token": "widget_abc123def456",
  "expires_at": "2025-01-24T10:00:00Z",
  "widget_url": "https://epoint.az/widget?token=abc123"
}</code></pre>
            </div>
        </section>

        <!-- Checkout Request -->
        <section id="checkout-request" class="doc-section">
            <h2>🛒 Checkout Request</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/checkout</span>
            </div>

            <p>Checkout səhifəsinə yönləndirmək üçün token alır. Payment Request-ə oxşardır, sadələşdirilmiş checkout flow təmin edir.</p>

            <div class="info-box warning">
                <strong>💡 Payment Request vs Checkout:</strong><br>
                • <strong>Payment Request:</strong> Ödəniş səhifəsi URL-i qaytarır<br>
                • <strong>Checkout:</strong> Birbaşa checkout səhifəsi (daha sürətli)
            </div>

            <h3>Kod Nümunəsi</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->checkoutRequest([
    'amount' => 35.00,
    'currency' => 'AZN',
    'language' => 'az',
    'order_id' => 'CHECKOUT_' . time(),
    'description' => 'Checkout ödənişi'
]);

return redirect($result['response']['checkout_url']);</code></pre>
            </div>
        </section>

        <!-- Invoice Create -->
        <section id="invoice-create" class="doc-section">
            <h2>🧾 Create Invoice</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/invoices/create</span>
            </div>

            <p>Yeni faktura yaradır və müştəriyə göndərilə bilər (SMS və ya Email vasitəsilə).</p>

            <h3>Request Parametrləri</h3>
            <div class="param-table">
                <table>
                    <thead>
                        <tr>
                            <th>Parametr</th>
                            <th>Tip</th>
                            <th>Tələb</th>
                            <th>Təsvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>sum</code></td>
                            <td>float</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Faktura məbləği</td>
                        </tr>
                        <tr>
                            <td><code>display</code></td>
                            <td>integer</td>
                            <td><span class="badge badge-required">Required</span></td>
                            <td>Göstərmək (0/1)</td>
                        </tr>
                        <tr>
                            <td><code>name</code></td>
                            <td>string</td>
                            <td><span class="badge badge-optional">Optional</span></td>
                            <td>Müştəri adı</td>
                        </tr>
                        <tr>
                            <td><code>phone</code></td>
                            <td>string</td>
                            <td><span class="badge badge-optional">Optional</span></td>
                            <td>Telefon nömrəsi</td>
                        </tr>
                        <tr>
                            <td><code>email</code></td>
                            <td>string</td>
                            <td><span class="badge badge-optional">Optional</span></td>
                            <td>Email ünvanı</td>
                        </tr>
                        <tr>
                            <td><code>inn</code></td>
                            <td>string</td>
                            <td><span class="badge badge-optional">Optional</span></td>
                            <td>VÖEN</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3>Kod Nümunəsi</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->invoiceCreate([
    'sum' => 100.50,
    'display' => 1,
    'save_as_template' => 0,
    'name' => 'Əli Məmmədov',
    'phone' => '+994501234567',
    'email' => 'ali@example.com',
    'inn' => '1234567890',
    'description' => 'Xidmət haqqı - Yanvar 2025'
]);

$invoiceId = $result['response']['invoice_id'];
$paymentLink = $result['response']['payment_link'];</code></pre>
            </div>
        </section>

        <!-- Invoice Update -->
        <section id="invoice-update" class="doc-section">
            <h2>✏️ Update Invoice</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/invoices/update</span>
            </div>

            <p>Mövcud fakturanı yeniləyir.</p>

            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->invoiceUpdate([
    'id' => 12345,
    'sum' => 120.00,
    'name' => 'Yenilənmiş ad',
    'description' => 'Yenilənmiş təsvir'
]);</code></pre>
            </div>
        </section>

        <!-- Invoice View -->
        <section id="invoice-view" class="doc-section">
            <h2>👁️ View Invoice</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/invoices/view</span>
            </div>

            <p>Faktura detallarını görüntüləyir.</p>

            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->invoiceView(['id' => 12345]);</code></pre>
            </div>
        </section>

        <!-- Invoice List -->
        <section id="invoice-list" class="doc-section">
            <h2>📋 List Invoices</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/invoices/list</span>
            </div>

            <p>Bütün fakturaların siyahısını qaytarır.</p>

            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->invoiceList();

foreach ($result['response']['invoices'] as $invoice) {
    echo "Invoice #{$invoice['id']}: {$invoice['sum']} AZN\n";
}</code></pre>
            </div>
        </section>

        <!-- Invoice SMS -->
        <section id="invoice-sms" class="doc-section">
            <h2>📱 Send Invoice via SMS</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/invoices/send-sms</span>
            </div>

            <p>Fakturanı SMS vasitəsilə müştəriyə göndərir.</p>

            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->invoiceSendSms([
    'id' => 12345,
    'phone' => '+994501234567'
]);</code></pre>
            </div>
        </section>

        <!-- Invoice Email -->
        <section id="invoice-email" class="doc-section">
            <h2>📧 Send Invoice via Email</h2>
            <div class="endpoint-badge">
                <span class="method post">POST</span>
                <span class="path">/api/1/invoices/send-email</span>
            </div>

            <p>Fakturanı Email vasitəsilə müştəriyə göndərir.</p>

            <div class="code-block">
                <div class="code-header">
                    <span>PHP</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">$result = $epointService->invoiceSendEmail([
    'id' => 12345,
    'email' => 'customer@example.com'
]);</code></pre>
            </div>
        </section>

        <!-- Error Handling -->
        <section id="error-handling" class="doc-section">
            <h2>⚠️ Error Handling</h2>

            <h3>HTTP Status Kodları</h3>
            <div class="error-table">
                <table>
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Kod</th>
                            <th>Mənası</th>
                            <th>Həll</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="status-code error">400</span></td>
                            <td>BAD_REQUEST</td>
                            <td>Səhv parametrlər</td>
                            <td>Parametrləri yoxlayın</td>
                        </tr>
                        <tr>
                            <td><span class="status-code error">401</span></td>
                            <td>UNAUTHORIZED</td>
                            <td>İmza səhvdir</td>
                            <td>Public/Private key yoxlayın</td>
                        </tr>
                        <tr>
                            <td><span class="status-code error">422</span></td>
                            <td>VALIDATION_ERROR</td>
                            <td>Validasiya xətası</td>
                            <td>Parametr formatını yoxlayın</td>
                        </tr>
                        <tr>
                            <td><span class="status-code error">429</span></td>
                            <td>TOO_MANY_REQUESTS</td>
                            <td>Rate limit aşılıb</td>
                            <td>Bir az gözləyin</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Rate Limiting -->
        <section id="rate-limiting" class="doc-section">
            <h2>🚦 Rate Limiting</h2>

            <div class="rate-table">
                <table>
                    <thead>
                        <tr>
                            <th>Endpoint Tipi</th>
                            <th>Limit</th>
                            <th>Müddət</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Login</td>
                            <td>10 request</td>
                            <td>1 dəqiqə</td>
                        </tr>
                        <tr>
                            <td>Standard API</td>
                            <td>60 request</td>
                            <td>1 dəqiqə</td>
                        </tr>
                        <tr>
                            <td>Payment API</td>
                            <td>30 request</td>
                            <td>1 dəqiqə</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Examples -->
        <section id="examples" class="doc-section">
            <h2>💻 Kod Nümunələri</h2>

            <h3>PHP (Laravel)</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>PaymentController.php</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-php">use App\Services\EpointService;
use App\DTOs\PaymentRequestDTO;

public function createPayment(Request $request)
{
    $dto = PaymentRequestDTO::fromArray([
        'amount' => 10.50,
        'currency' => 'AZN',
        'language' => 'az',
        'order_id' => 'TEST_' . time(),
    ]);

    $result = $this->epointService->paymentRequest($dto->toArray());

    if ($result['response']['status'] === 'success') {
        return redirect($result['response']['payment_url']);
    }
}</code></pre>
            </div>

            <h3>cURL</h3>
            <div class="code-block">
                <div class="code-header">
                    <span>Bash</span>
                    <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                </div>
                <pre><code class="language-bash">curl -X POST https://epoint.az/api/1/request \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "data=eyJwdWJsaWNfa2V5Ijoi..." \
  -d "signature=YWJjMTIzZGVm..."</code></pre>
            </div>
        </section>

        <!-- Footer -->
        <div class="docs-footer">
            <div class="footer-links">
                <a href="{{ route('docs.quick-start') }}">Sürətli Başlanğıc</a>
                <a href="{{ route('docs.refactoring') }}">Refactoring Report</a>
                <a href="https://epoint.az" target="_blank">Epoint.az</a>
            </div>
            <p class="footer-text">Made with ❤️ in Azerbaijan 🇦🇿</p>
        </div>
    </div>
</div>

<style>
.docs-container {
    display: flex;
    min-height: 100vh;
    background: #f8f9fa;
}

.docs-sidebar {
    width: 280px;
    background: #2d3748;
    color: white;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
    padding: 20px;
}

.sidebar-header h3 {
    margin: 0 0 20px 0;
    font-size: 20px;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu li {
    margin-bottom: 8px;
}

.sidebar-menu li.menu-header {
    margin-top: 20px;
    margin-bottom: 10px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    color: #a0aec0;
}

.sidebar-menu a {
    color: #e2e8f0;
    text-decoration: none;
    display: block;
    padding: 8px 12px;
    border-radius: 6px;
    transition: all 0.2s;
}

.sidebar-menu a:hover {
    background: #4a5568;
    color: white;
}

.docs-content {
    margin-left: 280px;
    padding: 40px;
    flex: 1;
    max-width: 900px;
}

.docs-header {
    margin-bottom: 40px;
}

.docs-header h1 {
    font-size: 42px;
    margin-bottom: 15px;
    color: #1a202c;
}

.lead {
    font-size: 18px;
    color: #718096;
    margin-bottom: 20px;
}

.badge-group {
    display: flex;
    gap: 10px;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.badge-version {
    background: #3182ce;
    color: white;
}

.badge-status {
    background: #38a169;
    color: white;
}

.badge-required {
    background: #f56565;
    color: white;
}

.badge-optional {
    background: #718096;
    color: white;
}

.doc-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.doc-section h2 {
    font-size: 32px;
    margin-bottom: 20px;
    color: #2d3748;
    padding-bottom: 15px;
    border-bottom: 2px solid #e2e8f0;
}

.doc-section h3 {
    font-size: 22px;
    margin: 25px 0 15px 0;
    color: #2d3748;
}

.info-box {
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    border-left: 4px solid;
}

.info-box.info {
    background: #ebf8ff;
    border-color: #3182ce;
}

.info-box.warning {
    background: #fffaf0;
    border-color: #ed8936;
}

.code-block {
    background: #2d3748;
    border-radius: 8px;
    overflow: hidden;
    margin: 20px 0;
}

.code-header {
    background: #1a202c;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    color: #a0aec0;
}

.copy-btn {
    background: #4a5568;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
}

.copy-btn:hover {
    background: #718096;
}

.code-block pre {
    margin: 0;
    padding: 20px;
    overflow-x: auto;
}

.code-block code {
    color: #e2e8f0;
    font-family: 'Consolas', 'Monaco', monospace;
    font-size: 14px;
    line-height: 1.6;
}

.endpoint-badge {
    display: flex;
    gap: 10px;
    align-items: center;
    margin: 15px 0;
}

.method {
    padding: 6px 12px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 13px;
}

.method.post {
    background: #38a169;
    color: white;
}

.path {
    font-family: 'Consolas', 'Monaco', monospace;
    font-size: 16px;
    color: #2d3748;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

table thead {
    background: #f7fafc;
}

table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #2d3748;
    border-bottom: 2px solid #e2e8f0;
}

table td {
    padding: 12px;
    border-bottom: 1px solid #e2e8f0;
}

table tbody tr:hover {
    background: #f7fafc;
}

.status-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin: 20px 0;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 15px;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    min-width: 80px;
    text-align: center;
}

.status-badge.new {
    background: #bee3f8;
    color: #2c5282;
}

.status-badge.pending {
    background: #feebc8;
    color: #7c2d12;
}

.status-badge.paid {
    background: #c6f6d5;
    color: #22543d;
}

.status-badge.failed {
    background: #fed7d7;
    color: #742a2a;
}

.status-code {
    padding: 4px 8px;
    border-radius: 4px;
    font-family: 'Consolas', monospace;
    font-weight: bold;
}

.status-code.error {
    background: #fed7d7;
    color: #742a2a;
}

.quick-links {
    display: flex;
    gap: 15px;
    margin: 20px 0;
}

.btn {
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    transition: all 0.2s;
}

.btn-primary {
    background: #3182ce;
    color: white;
}

.btn-primary:hover {
    background: #2c5282;
}

.btn-secondary {
    background: #718096;
    color: white;
}

.btn-secondary:hover {
    background: #4a5568;
}

.docs-footer {
    margin-top: 60px;
    padding-top: 30px;
    border-top: 2px solid #e2e8f0;
    text-align: center;
}

.footer-links {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 20px;
}

.footer-links a {
    color: #3182ce;
    text-decoration: none;
    font-weight: 600;
}

.footer-links a:hover {
    text-decoration: underline;
}

.footer-text {
    color: #718096;
    margin: 0;
}

@media (max-width: 768px) {
    .docs-sidebar {
        display: none;
    }

    .docs-content {
        margin-left: 0;
        padding: 20px;
    }
}
</style>

<script>
function copyCode(button) {
    const codeBlock = button.closest('.code-block').querySelector('code');
    const text = codeBlock.textContent;

    navigator.clipboard.writeText(text).then(() => {
        button.textContent = 'Copied!';
        setTimeout(() => {
            button.textContent = 'Copy';
        }, 2000);
    });
}

// Smooth scroll
document.querySelectorAll('.sidebar-menu a').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endsection
