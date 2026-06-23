<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Component extends Model
{
    protected $fillable = [
        'ckd_model_id',
        'code',
        'name',
        'expected_qty',
        'description',
        'is_active',
    ];

    protected $casts = [
        'expected_qty' => 'integer',
        'is_active'    => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function ckdModel(): BelongsTo
    {
        return $this->belongsTo(CkdModel::class, 'ckd_model_id');
    }

    public function inspectionItems(): HasMany
    {
        return $this->hasMany(InspectionItem::class, 'component_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForModel($query, int $ckdModelId)
    {
        return $query->where('ckd_model_id', $ckdModelId);
    }
}
