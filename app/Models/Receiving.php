<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Receiving extends Model
{
    protected $fillable = ['receiving_no', 'container_no', 'ckd_model_id', 'receive_date', 'status', 'created_by', 'notes', 'deleted'];

    protected $casts = [
        'receive_date' => 'date',
        'deleted' => 'boolean',
    ];

    // ─── Status constants ─────────────────────────────────────────────────────

    const STATUS_RECEIVED = 'RECEIVED';
    const STATUS_INSPECTION_OPEN = 'INSPECTION_OPEN';
    const STATUS_CLOSED = 'CLOSED';

    // ─── Relationships ────────────────────────────────────────────────────────

    public function ckdModel(): BelongsTo
    {
        return $this->belongsTo(CkdModel::class, 'ckd_model_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function inspection(): HasOne
    {
        return $this->hasOne(Inspection::class, 'receiving_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_INSPECTION_OPEN);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * Auto-generate receiving number: RCV-YYYYMMDD-NNN
     */
    public static function generateNo(): string
    {
        $today = now()->format('Ymd');
        $prefix = "RCV-{$today}-";
        $lastNo = static::where('receiving_no', 'like', $prefix . '%')
            ->orderByDesc('receiving_no')
            ->value('receiving_no');

        $seq = $lastNo ? ((int) substr($lastNo, -3)) + 1 : 1;

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function scopeActive($query)
    {
        return $query->where('deleted', false);
    }
}
