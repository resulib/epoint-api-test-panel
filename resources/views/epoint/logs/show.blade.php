@extends('layouts.app')

@section('content')
    <div class="main-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>📄 Log Details #{{ $log->id }}</h2>
                <p class="text-muted mb-0">{{ $log->created_at->format('Y-m-d H:i:s') }}</p>
            </div>
            <div>
                <a href="{{ route('epoint.logs.index') }}" class="btn btn-secondary">
                    ← Back to Logs
                </a>
                <form method="POST" action="{{ route('epoint.logs.destroy', $log->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this log?')">
                        🗑️ Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>API:</strong><br>
                        <span class="badge bg-secondary">{{ $log->api_name }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Endpoint:</strong><br>
                        <code>{{ $log->api_endpoint }}</code>
                    </div>
                    <div class="col-md-3">
                        <strong>Public Key:</strong><br>
                        <code>{{ $log->public_key_used }}</code>
                        @if($log->used_custom_keys)
                            <br><span class="badge bg-warning">Custom Keys</span>
                        @else
                            <br><span class="badge bg-success">Default Keys</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong><br>
                        <span class="badge bg-{{ $log->status_badge_class }}">
                        {{ ucfirst($log->status) }}
                    </span>
                    </div>
                    <div class="col-md-3">
                        <strong>HTTP Status:</strong><br>
                        <span class="badge bg-{{ $log->response_status_code == 200 ? 'success' : 'danger' }}">
                        {{ $log->response_status_code }}
                    </span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <strong>Order ID:</strong><br>
                        {{ $log->order_id ?? '-' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Transaction ID:</strong><br>
                        {{ $log->transaction_id ?? '-' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Amount:</strong><br>
                        {{ $log->amount ? number_format($log->amount, 2) . ' AZN' : '-' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Execution Time:</strong><br>
                        {{ $log->formatted_execution_time }}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>IP Address:</strong><br>
                        {{ $log->ip_address }}
                    </div>
                    <div class="col-md-6">
                        <strong>User Agent:</strong><br>
                        <small>{{ $log->user_agent }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="logTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#request-tab">
                    📤 Request
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#response-tab">
                    📥 Response
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#signature-tab">
                    🔐 Signature
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Request Tab -->
            <div class="tab-pane fade show active" id="request-tab">
                <div class="card">
                    <div class="card-header">
                        <h5>Request Parameters</h5>
                    </div>
                    <div class="card-body">
                        <pre><code class="language-json">{{ json_encode($log->request_params, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Encoded Data (Base64)</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" rows="5" readonly>{{ $log->request_data }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Response Tab -->
            <div class="tab-pane fade" id="response-tab">
                <div class="card">
                    <div class="card-header">
                        <h5>API Response</h5>
                    </div>
                    <div class="card-body">
                        <pre><code class="language-json">{{ json_encode($log->response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                </div>
            </div>

            <!-- Signature Tab -->
            <div class="tab-pane fade" id="signature-tab">
                <div class="card">
                    <div class="card-header">
                        <h5>Request Signature</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" rows="3" readonly>{{ $log->request_signature }}</textarea>
                        <div class="alert alert-info mt-3">
                            <strong>Generation formula:</strong><br>
                            <code>base64_encode(sha1(private_key + data + private_key, 1))</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
