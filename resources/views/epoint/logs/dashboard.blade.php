@extends('layouts.app')

@section('content')
    <div class="main-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📈 Dashboard</h2>
            <a href="{{ route('epoint.logs.index') }}" class="btn btn-primary">
                📊 View Logs
            </a>
        </div>

        <!-- Charts Row 1 -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Logs by API Endpoint</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="endpointChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Logs by Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Logs Over Time (Last 7 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="timelineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="card">
            <div class="card-header">
                <h5>Recent Logs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>Time</th>
                            <th>API</th>
                            <th>Order ID</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($recentLogs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('H:i:s') }}</td>
                                <td>{{ $log->api_name }}</td>
                                <td>{{ $log->order_id ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $log->status_badge_class }}">
                                        {{ $log->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Endpoint chart
        new Chart(document.getElementById('endpointChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($logsByEndpoint->pluck('api_name')) !!},
                datasets: [{
                    label: 'Request Count',
                    data: {!! json_encode($logsByEndpoint->pluck('count')) !!},
                    backgroundColor: 'rgba(102, 126, 234, 0.8)'
                }]
            }
        });

        // Status chart
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($logsByStatus->pluck('status')) !!},
                datasets: [{
                    data: {!! json_encode($logsByStatus->pluck('count')) !!},
                    backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#6c757d']
                }]
            }
        });

        // Timeline chart
        new Chart(document.getElementById('timelineChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($logsByDate->pluck('date')) !!},
                datasets: [{
                    label: 'Requests',
                    data: {!! json_encode($logsByDate->pluck('count')) !!},
                    borderColor: 'rgba(102, 126, 234, 1)',
                    backgroundColor: 'rgba(102, 126, 234, 0.2)',
                    tension: 0.4
                }]
            }
        });
    </script>
@endpush
