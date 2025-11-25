<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\EpointLogRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EpointLogsController extends Controller
{
    public function __construct(
        protected EpointLogRepositoryInterface $logRepository
    ) {}

    /**
     * Display logs list with filters
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'endpoint',
            'status',
            'transaction_id',
            'order_id',
            'date_from',
            'date_to'
        ]);

        $logs = $this->logRepository->getWithFilters($filters, 20);
        $stats = $this->logRepository->getStatistics();
        $endpoints = $this->logRepository->getUniqueEndpoints();

        return view('epoint.logs.index', compact('logs', 'stats', 'endpoints'));
    }

    /**
     * Show single log details
     */
    public function show(int $id): View
    {
        $log = $this->logRepository->findById($id);

        if (!$log) {
            abort(404, 'Log tapılmadı');
        }

        return view('epoint.logs.show', compact('log'));
    }

    /**
     * Delete log
     */
    public function destroy(int $id): RedirectResponse
    {
        $deleted = $this->logRepository->delete($id);

        if (!$deleted) {
            return redirect()->route('epoint.logs.index')
                ->with('error', 'Log silinə bilmədi');
        }

        return redirect()->route('epoint.logs.index')
            ->with('success', 'Log uğurla silindi');
    }

    /**
     * Clear all logs
     */
    public function clear(Request $request): RedirectResponse
    {
        if (!$request->has('confirm') || $request->confirm !== 'yes') {
            return redirect()->route('epoint.logs.index')
                ->with('error', 'Log-ları silmək üçün təsdiq tələb olunur');
        }

        $this->logRepository->truncate();

        return redirect()->route('epoint.logs.index')
            ->with('success', 'Bütün log-lar təmizləndi');
    }

    /**
     * Export logs to JSON
     */
    public function export(Request $request): JsonResponse
    {
        $filters = $request->only([
            'endpoint',
            'status',
            'date_from',
            'date_to'
        ]);

        $logs = $this->logRepository->getWithFilters($filters, PHP_INT_MAX);

        $filename = 'epoint_logs_' . date('Y-m-d_His') . '.json';

        return response()->json($logs->items(), 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Dashboard with charts
     */
    public function dashboard(): View
    {
        $dashboardData = $this->logRepository->getDashboardData();

        return view('epoint.logs.dashboard', [
            'logsByEndpoint' => $dashboardData['logs_by_endpoint'],
            'logsByStatus' => $dashboardData['logs_by_status'],
            'logsByDate' => $dashboardData['logs_by_date'],
            'recentLogs' => $dashboardData['recent_logs'],
            'avgExecutionTime' => $dashboardData['avg_execution_time'],
        ]);
    }
}
