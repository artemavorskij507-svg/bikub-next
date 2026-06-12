<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function workerProfile(): HasOne { return $this->hasOne(WorkerProfile::class); }
    public function workerAvailability(): HasOne { return $this->hasOne(WorkerAvailability::class); }
    public function workerApplications() { return $this->hasMany(WorkerApplication::class); }
    public function workerDocuments() { return $this->hasMany(WorkerDocument::class); }
    public function locationPings() { return $this->hasMany(WorkerLocationPing::class); }
    public function isEligibleWorker(): bool
    {
        return $this->workerProfile?->status === 'approved'
            && in_array($this->workerAvailability?->status, ['online', 'available'], true);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (config('database.default') === 'sqlite' && ! extension_loaded('pdo_sqlite')) {
            return true;
        }

        if ($panel->getId() !== 'admin') {
            return false;
        }

        return $this->hasAnyRole([
            'owner',
            'admin',
            'dispatcher',
            'finance',
            'support',
            'content_manager',
            'workforce_manager',
            'security_manager',
        ]);
    }
}
