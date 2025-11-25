<?php

namespace App\Repositories\Contracts;

use App\Models\EpointLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EpointLogRepositoryInterface
{
    /**
     * Get all logs with pagination
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator;

    /**
     * Get logs with filters
     */
    public function getWithFilters(array $filters, int $perPage = 20): LengthAwarePaginator;

    /**
     * Find log by ID
     */
    public function findById(int $id): ?EpointLog;

    /**
     * Create new log entry
     */
    public function create(array $data): EpointLog;

    /**
     * Delete log by ID
     */
    public function delete(int $id): bool;

    /**
     * Clear all logs
     */
    public function truncate(): void;

    /**
     * Get statistics
     */
    public function getStatistics(): array;

    /**
     * Get unique endpoints
     */
    public function getUniqueEndpoints(): Collection;

    /**
     * Get logs by endpoint
     */
    public function getByEndpoint(string $endpoint): Collection;

    /**
     * Get logs by status
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get logs by date range
     */
    public function getByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Get dashboard data
     */
    public function getDashboardData(): array;
}
