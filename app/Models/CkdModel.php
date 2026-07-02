<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CkdModel extends Model
{
    protected $table = 'ckd_models';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function components(): HasMany
    {
        return $this->hasMany(Component::class, 'ckd_model_id');
    }

    public function activeComponents(): HasMany
    {
        return $this->hasMany(Component::class, 'ckd_model_id')
                    ->where('is_active', true);
    }

    public function receivings(): HasMany
    {
        return $this->hasMany(Receiving::class, 'ckd_model_id')
                ->where('deleted', false);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
