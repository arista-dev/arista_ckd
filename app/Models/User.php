<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = [
        'username',
        'password',
        'name',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Role helpers ─────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInspector(): bool
    {
        return $this->role === 'inspector';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function receivingsCreated(): HasMany
    {
        return $this->hasMany(Receiving::class, 'created_by');
    }

    public function inspectionsHandled(): HasMany
    {
        return $this->hasMany(Inspection::class, 'inspector_id');
    }

    public function inspectionsApproved(): HasMany
    {
        return $this->hasMany(Inspection::class, 'approved_by');
    }

    public function inspectionsRejected(): HasMany
    {
        return $this->hasMany(Inspection::class, 'rejected_by');
    }
}
