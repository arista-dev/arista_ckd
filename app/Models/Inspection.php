<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inspection extends Model
{
    protected $fillable = [
        'inspection_no',
        'receiving_id',
        'status',
        'inspector_id',
        'inspected_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'notes',
        'vin'
    ];

    protected $casts = [
        'inspected_at' => 'datetime',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
    ];

    // ─── Status constants ─────────────────────────────────────────────────────

    const STATUS_OPEN             = 'OPEN';
    const STATUS_WAITING_APPROVAL = 'WAITING_APPROVAL';
    const STATUS_CLOSED           = 'CLOSED';
    const STATUS_CANCEL           = 'CANCELLED';

    // ─── Relationships ────────────────────────────────────────────────────────

    public function receiving(): BelongsTo
    {
        return $this->belongsTo(Receiving::class, 'receiving_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InspectionItem::class, 'inspection_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeWaitingApproval($query)
    {
        return $query->where('status', self::STATUS_WAITING_APPROVAL);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public function scopeByModel($query, string $modelCode)
    {
        return $query->whereHas('receiving.ckdModel', fn($q) => $q->where('code', $modelCode));
    }

    public function scopeByDateRange($query, ?string $from, ?string $to)
    {
        if ($from) $query->whereDate('inspected_at', '>=', $from);
        if ($to)   $query->whereDate('inspected_at', '<=', $to);
        return $query;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isWaitingApproval(): bool
    {
        return $this->status === self::STATUS_WAITING_APPROVAL;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function totalShortage(): int
    {
        return $this->items->where('status', 'SHORT')->count();
    }

    public function totalDamage(): int
    {
        return $this->items->where('status', 'DAMAGE')->count();
    }

    /**
     * Auto-generate inspection number: INS-YYYYMMDD-NNN
     */
    public static function generateNo(): string
    {
        $today  = now()->format('Ymd');
        $prefix = "INS-{$today}-";
        $lastNo = static::where('inspection_no', 'like', $prefix . '%')
                        ->orderByDesc('inspection_no')
                        ->value('inspection_no');

        $seq = $lastNo ? ((int) substr($lastNo, -3)) + 1 : 1;

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
