<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpointLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_endpoint',
        'api_name',
        'public_key_used',        // ← Yeni
        'used_custom_keys',       // ← Yeni
        'request_params',
        'request_data',
        'request_signature',
        'response_data',
        'response_status_code',
        'transaction_id',
        'order_id',
        'amount',
        'status',
        'ip_address',
        'user_agent',
        'execution_time',
    ];

    protected $casts = [
        'request_params' => 'array',
        'response_data' => 'array',
        'amount' => 'decimal:2',
        'execution_time' => 'decimal:3',
        'used_custom_keys' => 'boolean',  // ← Yeni
    ];

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by API endpoint
     */
    public function scopeByEndpoint($query, $endpoint)
    {
        return $query->where('api_endpoint', $endpoint);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get formatted execution time
     */
    public function getFormattedExecutionTimeAttribute()
    {
        return number_format($this->execution_time, 3) . ' ms';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'success' => 'success',
            'failed', 'error' => 'danger',
            'new', 'pending' => 'warning',
            default => 'secondary'
        };
    }
}
