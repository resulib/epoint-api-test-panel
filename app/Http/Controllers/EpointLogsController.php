<?php

namespace App\Http\Controllers;

use App\Models\EpointLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EpointLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = EpointLog::query()->orderBy('created_at', 'desc');

        // Filter by endpoint
        if ($request->filled('endpoint')) {
            $query->where('api_endpoint', $request->endpoint);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by transaction ID
        if ($request->filled('transaction_id')) {
            $query->where('transaction_id', 'like', '%' . $request->transaction_id . '%');
        }

        // Filter by order ID
        if ($request->filled('order_id')) {
            $query->where('order_id', 'like', '%' . $request->order_id . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => EpointLog::count(),
            'today' => EpointLog::whereDate('created_at', today())->count(),
            'success' => EpointLog::where('status', 'success')->count(),
            'failed' => EpointLog::whereIn('status', ['failed', 'error'])->count(),
            'avg_execution_time' => EpointLog::avg('execution_time'),
        ];

        // Get unique endpoints for filter dropdown
        $endpoints = EpointLog::select('api_endpoint', 'api_name')
            ->distinct()
            ->orderBy('api_name')
            ->get();

        return view('epoint.logs.index', compact('logs', 'stats', 'endpoints'));
    }

    /**
     * Show single log details
     */
    public function show($id)
    {
        $log = EpointLog::findOrFail($id);
        return view('epoint.logs.show', compact('log'));
    }

    /**
     * Delete log
     */
    public function destroy($id)
    {
        $log = EpointLog::findOrFail($id);
        $log->delete();

        return redirect()->route('epoint.logs.index')
            ->with('success', 'Log deleted successfully');
    }

    /**
     * Clear all logs
     */
    public function clear(Request $request)
    {
        if ($request->has('confirm') && $request->confirm === 'yes') {
            EpointLog::truncate();
            return redirect()->route('epoint.logs.index')
                ->with('success', 'All logs cleared successfully');
        }

        return redirect()->route('epoint.logs.index')
            ->with('error', 'Confirmation required to clear logs');
    }

    /**
     * Export logs to JSON
     */
    public function export(Request $request)
    {
        $query = EpointLog::query()->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('endpoint')) {
            $query->where('api_endpoint', $request->endpoint);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->get();

        $filename = 'epoint_logs_' . date('Y-m-d_His') . '.json';

        return response()->json($logs, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Dashboard with charts
     */
    public function dashboard()
    {
        // Logs by endpoint
        $logsByEndpoint = EpointLog::select('api_name', DB::raw('count(*) as count'))
            ->groupBy('api_name')
            ->orderBy('count', 'desc')
            ->get();

        // Logs by status
        $logsByStatus = EpointLog::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Logs by date (last 7 days)
        $logsByDate = EpointLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Recent logs
        $recentLogs = EpointLog::orderBy('created_at', 'desc')->limit(10)->get();

        // Average execution time by endpoint
        $avgExecutionTime = EpointLog::select('api_name', DB::raw('AVG(execution_time) as avg_time'))
            ->groupBy('api_name')
            ->orderBy('avg_time', 'desc')
            ->get();

        return view('epoint.logs.dashboard', compact(
            'logsByEndpoint',
            'logsByStatus',
            'logsByDate',
            'recentLogs',
            'avgExecutionTime'
        ));
    }
}
