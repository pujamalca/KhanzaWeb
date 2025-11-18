<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PcareActivityLog extends Model
{
    protected $table = 'pcare_activity_log';

    protected $fillable = [
        'no_rawat',
        'no_kunjungan',
        'activity_type',
        'action',
        'endpoint',
        'status',
        'http_code',
        'request_data',
        'response_data',
        'error_message',
        'user',
    ];

    protected $casts = [
        'http_code' => 'integer',
    ];

    /**
     * Scopes
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByActivityType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Accessors
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'success' => 'success',
            'failed' => 'danger',
            default => 'gray'
        };
    }

    public function getRequestDataArrayAttribute(): ?array
    {
        return $this->request_data ? json_decode($this->request_data, true) : null;
    }

    public function getResponseDataArrayAttribute(): ?array
    {
        return $this->response_data ? json_decode($this->response_data, true) : null;
    }
}
