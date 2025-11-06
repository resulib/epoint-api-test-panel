@extends('layouts.app')

@section('content')
    <div class="main-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>📊 Epoint API Logs</h2>
                <p class="text-muted mb-0">Monitor all API requests and responses</p>
            </div>
            <div>
                <a href="{{ route('epoint.logs.dashboard') }}" class="btn btn-info me-2">
                    📈 Dashboard
                </a>
                <a href="{{ route('epoint.test') }}" class="btn btn-primary me-2">
                    🧪 Test Panel
                </a>
                <a href="{{ route('epoint.logs.export', request()->query()) }}" class="btn btn-success me-2">
                    📥 Export JSON
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearModal">
                    🗑️ Clear Logs
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Logs</h5>
                        <h2 class="mb-0">{{ number_format($stats['total']) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today</h5>
                        <h2 class="mb-0">{{ number_format($stats['today']) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Success</h5>
                        <h2 class="mb-0">{{ number_format($stats['success']) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Failed</h5>
                        <h2 class="mb-0">{{ number_format($stats['failed']) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">🔍 Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('epoint.logs.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">API Endpoint</label>
                            <select name="endpoint" class="form-select">
                                <option value="">All Endpoints</option>
                                @foreach($endpoints as $endpoint)
                                    <option value="{{ $endpoint->api_endpoint }}" {{ request('endpoint') == $endpoint->api_endpoint ? 'selected' : '' }}>
                                        {{ $endpoint->api_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Error</option>
                                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Transaction ID</label>
                            <input type="text" name="transaction_id" class="form-control" value="{{ request('transaction_id') }}" placeholder="te000000001">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Order ID</label>
                            <input type="text" name="order_id" class="form-control" value="{{ request('order_id') }}" placeholder="ORDER123">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date Range</label>
                            <div class="input-group">
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="{{ route('epoint.logs.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date/Time</th>
                            <th>API</th>
                            <th>Public Key</th>
                            <th>Order ID</th>
                            <th>Transaction</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Exec Time</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>
                                    <small>{{ $log->created_at->format('Y-m-d') }}</small><br>
                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $log->api_name }}</span>
                                </td>
                                <td>  <!-- ✅ Yeni -->
                                    <code>{{ $log->public_key_used }}</code>
                                    @if($log->used_custom_keys)
                                        <br><span class="badge bg-warning">Custom</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->order_id)
                                        <code>{{ $log->order_id }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->transaction_id)
                                        <code>{{ $log->transaction_id }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->amount)
                                        <strong>{{ number_format($log->amount, 2) }} AZN</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $log->status_badge_class }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $log->formatted_execution_time }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('epoint.logs.show', $log->id) }}" class="btn btn-sm btn-info">
                                        👁️ View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    No logs found
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Logs Confirmation Modal -->
    <div class="modal fade" id="clearModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Clear All Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>ALL</strong> logs?</p>
                    <p class="text-danger">This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('epoint.logs.clear') }}">
                        @csrf
                        <input type="hidden" name="confirm" value="yes">
                        <button type="submit" class="btn btn-danger">Yes, Clear All</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
