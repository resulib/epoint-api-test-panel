@extends('layouts.app')

@section('content')
    <div class="main-container">
        <form method="POST" action="{{ route('epoint.invoice.execute') }}" id="invoiceForm" enctype="multipart/form-data">
            @csrf

            <!-- API Selector -->
            <div class="api-selector">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Select Invoice API Endpoint:</label>
                        <select name="api" id="apiSelect" class="form-select form-select-lg" required>
                            <option value="">-- Choose Invoice API --</option>
                            @foreach($invoiceApis as $key => $api)
                                <option value="{{ $key }}"
                                        data-endpoint="{{ $api['endpoint'] }}"
                                        data-full-url="{{ $api['full_url'] }}"
                                    {{ (isset($selectedApi) && $key == $selectedApi) ? 'selected' : '' }}>
                                   {{ $api['name'] }} <strong>({{ $api['full_url'] }})</strong>
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
                    🚀 Execute Invoice API Call
                </button>
            </div>

            <!-- Custom Keys Section (Accordion) -->
            <div class="accordion mb-4" id="customKeysAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingKeys">
                        <button class="accordion-button collapsed bg-warning" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKeys" aria-expanded="false" aria-controls="collapseKeys">
                            🔑 Authentication Keys
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
                                    👁️ Show/Hide Private Key
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </form>

        @if(isset($result))
            <div class="result-container">
                <h4 class="text-white mb-3">📊 API Response:</h4>

                <!-- Show which keys were used -->
                @if(isset($result['request']['used_custom_keys']) && $result['request']['used_custom_keys'])
                    <div class="alert alert-warning">
                        <strong>⚠️ Used Custom Keys:</strong><br>
                        Public Key: <code>{{ $result['request']['public_key'] }}</code>
                    </div>
                @else
                    <div class="alert alert-success">
                        <strong>✅ Used Default Keys from .env:</strong><br>
                        Public Key: <code>{{ $result['request']['public_key'] }}</code>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <ul class="nav nav-tabs mb-0" id="resultTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#response-tab">Response</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#request-tab">Request</button>
                        </li>
                        @if(isset($result['response']['url']))
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#invoice-url-tab">Invoice URL</button>
                            </li>
                        @endif
                    </ul>
                    <a href="{{ route('epoint.invoice') }}" class="btn btn-secondary">
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
                    @if(isset($result['response']['url']))
                        <div class="tab-pane fade" id="invoice-url-tab">
                            <div class="alert alert-success">
                                <strong>✅ Invoice Created Successfully!</strong><br>
                                <strong>Invoice URL:</strong><br>
                                <code>{{ $result['response']['url'] }}</code>
                            </div>
                            <a href="{{ $result['response']['url'] }}" target="_blank" class="btn btn-success btn-lg w-100">
                                🧾 Open Invoice Page
                            </a>

                            @if(isset($result['response']['data']['id']))
                                <div class="alert alert-info mt-3">
                                    <strong>Invoice ID:</strong> <code>{{ $result['response']['data']['id'] }}</code>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="alert alert-{{ $result['status_code'] == 200 ? 'success' : 'danger' }} mt-3">
                    <strong>HTTP Status:</strong> {{ $result['status_code'] }}
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

            if (toggleButton && privateKeyInput) {
                toggleButton.addEventListener('click', function () {
                    privateKeyInput.type = privateKeyInput.type === 'password' ? 'text' : 'password';
                });
            }
        });
    </script>

    <script>
        const invoiceApis = @json($invoiceApis);

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

            const api = invoiceApis[selectedApi];

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
                        Object.entries(paramConfig.options).forEach(([value, label]) => {
                            html += `<option value="${value}" ${paramConfig.default == value ? 'selected' : ''}>${label}</option>`;
                        });
                        html += `</select>`;
                    } else if (paramConfig.type === 'file') {
                        html += `
                    <input
                        type="file"
                        name="${paramName}"
                        class="form-control"
                        ${paramConfig.required ? 'required' : ''}
                        accept="image/*"
                        multiple
                    >
                    <small class="text-muted">Supported: jpg, png, jpeg, svg, bmp</small>
                `;
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
