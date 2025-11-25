@extends('layouts.app')

@section('content')
    <div class="main-container">
        <form method="POST" action="{{ route('epoint.checkout.execute') }}" id="apiForm">
            @csrf

            <!-- API Selector -->
            <div class="api-selector">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Select Checkout API Endpoint:</label>
                        <select name="api" id="apiSelect" class="form-select form-select-lg" required>
                            <option value="">-- Choose API --</option>
                            @foreach($checkoutApis as $key => $api)
                                <option value="{{ $key }}"
                                        data-endpoint="{{ $api['endpoint'] }}"
                                        data-full-url="{{ $api['full_url'] }}"
                                    {{ (isset($selectedApi) && $key == $selectedApi) ? 'selected' : '' }}>
                                    {{ $api['name'] }} ({{ $api['full_url'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="alert alert-info mb-0" id="urlDisplay" style="display: none;">
                            <strong>Full URL:</strong> <code id="fullUrlText"></code>
                        </div>
                    </div>
                </div>
            </div>

            <div id="parametersContainer"></div>

            <div class="text-center mt-4 mb-4">
                <button type="submit" class="btn btn-primary btn-lg btn-execute">
                    Execute Checkout API
                </button>
            </div>

            <!-- Custom Keys Section (Accordion) -->
            <div class="accordion mb-4" id="customKeysAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingKeys">
                        <button class="accordion-button collapsed bg-warning" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKeys" aria-expanded="false" aria-controls="collapseKeys">
                            Authentication Keys
                            <small class="text-muted ms-2">(Optional - uses .env keys by default)</small>
                        </button>
                    </h2>
                    <div id="collapseKeys" class="accordion-collapse collapse" aria-labelledby="headingKeys" data-bs-parent="#customKeysAccordion">
                        <div class="accordion-body">

                            <div class="alert alert-info">
                                <strong>Default:</strong> Using keys from <code>.env</code> file<br>
                                <code>EPOINT_PUBLIC_KEY={{ config('services.epoint.public_key') }}</code><br>
                                <small class="text-muted">Leave fields below empty to use default keys</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Custom Public Key</label>
                                    <input
                                        type="text"
                                        name="custom_public_key"
                                        class="form-control"
                                        placeholder="i000000002"
                                        value="{{ $customPublicKey ?? '' }}"
                                        autocomplete="off"
                                    >
                                    <small class="text-muted">Example: i000000001, i000000002</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Custom Private Key</label>
                                    <input
                                        type="password"
                                        name="custom_private_key"
                                        class="form-control"
                                        placeholder="your_private_key_here"
                                        value="{{ $customPrivateKey ?? '' }}"
                                        autocomplete="new-password"
                                    >
                                    <small class="text-muted">Your merchant private key</small>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-secondary" id="togglePrivateKey">
                                    Show/Hide Private Key
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


        </form>

        @if(isset($result))
            <div class="result-container">
                <h4 class="text-white mb-3">API Response:</h4>

                <!-- Status Message -->
                @if(isset($result['response']['status']))
                    @if($result['response']['status'] === 'success' && isset($result['response']['redirect_url']))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">Payment Request Successful!</h5>
                            <p class="mb-2">Transaction created successfully. Redirect URL received:</p>
                            <code class="d-block mb-2">{{ $result['response']['redirect_url'] }}</code>
                            <hr>
                            <a href="{{ $result['response']['redirect_url'] }}" target="_blank" class="btn btn-success btn-sm">
                                Open Payment Page
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @elseif($result['response']['status'] === 'error')
                        <div class="alert alert-danger">
                            <strong>Error:</strong> {{ $result['response']['message'] ?? 'Unknown error occurred' }}
                        </div>
                    @endif
                @endif

                <!-- Show which keys were used -->
                @if(isset($result['request']['used_custom_keys']) && $result['request']['used_custom_keys'])
                    <div class="alert alert-warning">
                        <strong>Used Custom Keys:</strong><br>
                        Public Key: <code>{{ $result['request']['public_key'] }}</code>
                    </div>
                @else
                    <div class="alert alert-success">
                        <strong>Used Default Keys from .env:</strong><br>
                        Public Key: <code>{{ $result['request']['public_key'] }}</code>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <ul class="nav nav-tabs mb-0" id="resultTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#response-tab">Response JSON</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#request-tab">Request</button>
                        </li>
                        @if(isset($result['response']['raw_response']))
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#raw-tab">Raw Response</button>
                            </li>
                        @endif
                        @if(isset($result['response']['redirect_url']) || isset($result['response']['checkout_url']))
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#redirect-tab">Redirect URL</button>
                            </li>
                        @endif
                    </ul>
                    <a href="{{ route('epoint.checkout') }}" class="btn btn-secondary">
                        🏠 Ana Səhifə
                    </a>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="response-tab">
                        <pre><code class="language-json">{{ json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                    <div class="tab-pane fade" id="request-tab">
                        <pre><code class="language-json">{{ json_encode($result['request'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                    @if(isset($result['response']['raw_response']))
                        <div class="tab-pane fade" id="raw-tab">
                            <div class="alert alert-warning">
                                <strong>Raw Response Body:</strong> API returned HTML instead of JSON
                            </div>

                            <!-- Check if response is HTML -->
                            @php
                                $rawBody = $result['response']['raw_response'];
                                $isHtml = preg_match('/<html|<form|<!DOCTYPE/i', $rawBody);
                            @endphp

                            @if($isHtml)
                                <div class="alert alert-info">
                                    <strong>HTML Response Detected:</strong> The API returned an HTML page (likely a redirect form)
                                </div>

                                <!-- Tabs for HTML view -->
                                <ul class="nav nav-pills mb-3">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#html-preview">Preview</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#html-source">HTML Source</button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="html-preview">
                                        <iframe
                                            srcdoc="{{ htmlspecialchars($rawBody) }}"
                                            style="width: 100%; height: 500px; border: 1px solid #ccc; background: white;"
                                            sandbox="allow-forms allow-scripts allow-same-origin"
                                        ></iframe>
                                    </div>
                                    <div class="tab-pane fade" id="html-source">
                                        <pre><code class="language-html">{{ $rawBody }}</code></pre>
                                    </div>
                                </div>
                            @else
                                <pre><code>{{ $rawBody }}</code></pre>
                            @endif
                        </div>
                    @endif
                    @if(isset($result['response']['redirect_url']) || isset($result['response']['checkout_url']))
                        <div class="tab-pane fade" id="redirect-tab">
                            <div class="alert alert-info">
                                <strong>Redirect URL:</strong><br>
                                <code>{{ $result['response']['redirect_url'] ?? $result['response']['checkout_url'] }}</code>
                            </div>
                            <a href="{{ $result['response']['redirect_url'] ?? $result['response']['checkout_url'] }}" target="_blank" class="btn btn-success btn-lg w-100 redirect-button">
                                Open Payment Page
                            </a>
                        </div>
                    @endif
                </div>

                <div class="alert alert-{{ $result['status_code'] == 200 ? 'success' : 'danger' }} mt-3">
                    <strong>HTTP Status:</strong> {{ $result['status_code'] }}
                    <br>
                    <strong>Execution Time:</strong> {{ number_format($result['execution_time'], 2) }}ms
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')

    <script>
        // Show/Hide private key button
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('togglePrivateKey');
            const privateKeyInput = document.querySelector('input[name="custom_private_key"]');

            toggleButton.addEventListener('click', function () {
                privateKeyInput.type = privateKeyInput.type === 'password' ? 'text' : 'password';
            });
        });
    </script>

    <script>
        const checkoutApis = @json($checkoutApis);

        // Toggle private key visibility
        document.getElementById('togglePrivateKey').addEventListener('click', function() {
            const input = document.querySelector('input[name="custom_private_key"]');
            input.type = input.type === 'password' ? 'text' : 'password';
        });

        // API selector logic
        document.getElementById('apiSelect').addEventListener('change', function() {
            const selectedApi = this.value;
            const container = document.getElementById('parametersContainer');
            const urlDisplay = document.getElementById('urlDisplay');
            const fullUrlText = document.getElementById('fullUrlText');

            if (!selectedApi) {
                container.innerHTML = '';
                urlDisplay.style.display = 'none';
                return;
            }

            const api = checkoutApis[selectedApi];

            fullUrlText.textContent = api.full_url;
            urlDisplay.style.display = 'block';

            let html = '<div class="row">';

            if (Object.keys(api.params).length === 0) {
                html += '<div class="col-12"><div class="alert alert-info">This API has no parameters</div></div>';
            } else {
                Object.entries(api.params).forEach(([paramName, paramConfig]) => {
                    html += `
                <div class="col-md-6 param-group">
                    <label class="form-label">
                        ${paramName}
                        ${paramConfig.required ? '<span class="text-danger">*</span>' : ''}
                    </label>
            `;

                    if (paramConfig.type === 'select') {
                        html += `<select name="${paramName}" class="form-select" ${paramConfig.required ? 'required' : ''}>`;
                        if (typeof paramConfig.options === 'object' && !Array.isArray(paramConfig.options)) {
                            Object.entries(paramConfig.options).forEach(([value, label]) => {
                                html += `<option value="${value}" ${paramConfig.default == value ? 'selected' : ''}>${label}</option>`;
                            });
                        } else {
                            paramConfig.options.forEach(option => {
                                html += `<option value="${option}" ${paramConfig.default == option ? 'selected' : ''}>${option}</option>`;
                            });
                        }
                        html += `</select>`;
                    } else {
                        html += `
                    <input
                        type="${paramConfig.type}"
                        name="${paramName}"
                        class="form-control"
                        value="${paramConfig.default || ''}"
                        ${paramConfig.required ? 'required' : ''}
                        ${paramConfig.type === 'number' ? 'step="0.01" min="0.01"' : ''}
                    >
                `;
                    }

                    html += `</div>`;
                });
            }

            html += '</div>';
            container.innerHTML = html;
        });

        @if(isset($selectedApi))
        document.getElementById('apiSelect').value = '{{ $selectedApi }}';
        document.getElementById('apiSelect').dispatchEvent(new Event('change'));
        @endif
    </script>
@endpush
