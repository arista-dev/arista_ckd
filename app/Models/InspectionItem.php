<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionItem extends Model
{
    protected $fillable = [
        'inspection_id',
        'component_id',
        'component_code',
        'component_name',
        'expected_qty',
        'actual_qty',
        'short_qty',
        'status',
        'damage_remark',
        'damage_photo',
    ];

    protected $casts = [
        'expected_qty' => 'integer',
        'actual_qty'   => 'integer',
        'short_qty'    => 'integer',
    ];

    // ─── Status constants ─────────────────────────────────────────────────────

    const STATUS_OK     = 'OK';
    const STATUS_SHORT  = 'SHORT';
    const STATUS_DAMAGE = 'DAMAGE';

    // ─── Relationships ────────────────────────────────────────────────────────

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class, 'component_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeShort($query)
    {
        return $query->where('status', self::STATUS_SHORT);
    }

    public function scopeDamage($query)
    {
        return $query->where('status', self::STATUS_DAMAGE);
    }

    public function scopeOk($query)
    {
        return $query->where('status', self::STATUS_OK);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isShort(): bool
    {
        return $this->status === self::STATUS_SHORT;
    }

    public function isDamage(): bool
    {
        return $this->status === self::STATUS_DAMAGE;
    }

    public function isOk(): bool
    {
        return $this->status === self::STATUS_OK;
    }

    public function hasDamagePhoto(): bool
    {
        return !empty($this->damage_photo);
    }

    public function damagePhotoUrl(): ?string
    {
        return $this->damage_photo
            ? asset('storage/damage/' . $this->damage_photo)
            : null;
    }

    /**
     * Recalculate short_qty and auto-set status before saving.
     */
    protected static function booted(): void
    {
        static::saving(function (self $item) {
            if ($item->actual_qty !== null) {
                $item->short_qty = max(0, $item->expected_qty - $item->actual_qty);

                // Auto SHORT when actual < expected
                if ($item->short_qty > 0 && $item->status !== self::STATUS_DAMAGE) {
                    $item->status = self::STATUS_SHORT;
                }
            }
        });
    }
}
