<?php

namespace App\Repositories;

use App\Models\EpointLog;
use App\Repositories\Contracts\EpointLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EpointLogRepository implements EpointLogRepositoryInterface
{
    /**
     * @var EpointLog
     */
    protected $model;

    public function __construct(EpointLog $model)
    {
        $this->model = $model;
    }

    /**
     * Get all logs with pagination
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get logs with filters
     */
    public function getWithFilters(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->query()->orderBy('created_at', 'desc');

        if (!empty($filters['endpoint'])) {
            $query->where('api_endpoint', $filters['endpoint']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['transaction_id'])) {
            $query->where('transaction_id', 'like', '%' . $filters['transaction_id'] . '%');
        }

        if (!empty($filters['order_id'])) {
            $query->where('order_id', 'like', '%' . $filters['order_id'] . '%');
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to'] . ' 23:59:59');
        }

        return $query->paginate($perPage);
    }

    /**
     * Find log by ID
     */
    public function findById(int $id): ?EpointLog
    {
        return $this->model->find($id);
    }

    /**
     * Create new log entry
     */
    public function create(array $data): EpointLog
    {
        return $this->model->create($data);
    }

    /**
     * Delete log by ID
     */
    public function delete(int $id): bool
    {
        $log = $this->findById($id);

        if (!$log) {
            return false;
        }

        return $log->delete();
    }

    /**
     * Clear all logs
     */
    public function truncate(): void
    {
        $this->model->truncate();
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'today' => $this->model->whereDate('created_at', today())->count(),
            'success' => $this->model->where('status', 'success')->count(),
            'failed' => $this->model->whereIn('status', ['failed', 'error'])->count(),
            'avg_execution_time' => round($this->model->avg('execution_time'), 3),
        ];
    }

    /**
     * Get unique endpoints
     */
    public function getUniqueEndpoints(): Collection
    {
        return $this->model
            ->select('api_endpoint', 'api_name')
            ->distinct()
            ->orderBy('api_name')
            ->get();
    }

    /**
     * Get logs by endpoint
     */
    public function getByEndpoint(string $endpoint): Collection
    {
        return $this->model
            ->where('api_endpoint', $endpoint)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get logs by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get logs by date range
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get dashboard data
     */
    public function getDashboardData(): array
    {
        return [
            'logs_by_endpoint' => $this->model
                ->select('api_name', DB::raw('count(*) as count'))
                ->groupBy('api_name')
                ->orderBy('count', 'desc')
                ->get(),

            'logs_by_status' => $this->model
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),

            'logs_by_date' => $this->model
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            'recent_logs' => $this->model
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),

            'avg_execution_time' => $this->model
                ->select('api_name', DB::raw('AVG(execution_time) as avg_time'))
                ->groupBy('api_name')
                ->orderBy('avg_time', 'desc')
                ->get(),
        ];
    }
}
