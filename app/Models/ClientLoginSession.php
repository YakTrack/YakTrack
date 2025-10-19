<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientLoginSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_user_id',
        'ip_address',
        'user_agent',
        'session_id',
        'logged_in_at',
        'logged_out_at',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'logged_in_at' => 'datetime',
        'logged_out_at' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the client user that owns this login session.
     */
    public function clientUser(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class);
    }

    /**
     * Scope to get active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get sessions for a specific client user.
     */
    public function scopeForClientUser($query, $clientUserId)
    {
        return $query->where('client_user_id', $clientUserId);
    }

    /**
     * Scope to get recent sessions.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('logged_in_at', '>=', now()->subDays($days));
    }

    /**
     * Mark session as logged out.
     */
    public function markAsLoggedOut(): void
    {
        $this->update([
            'logged_out_at' => now(),
            'is_active' => false,
        ]);
    }

    /**
     * Get session duration in minutes.
     */
    public function getDurationInMinutesAttribute(): ?int
    {
        if (!$this->logged_out_at) {
            return null;
        }

        return $this->logged_in_at->diffInMinutes($this->logged_out_at);
    }

    /**
     * Get formatted session duration.
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if (!$this->logged_out_at) {
            return 'Active';
        }

        $minutes = $this->duration_in_minutes;
        
        if ($minutes < 60) {
            return $minutes . ' minutes';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours < 24) {
            return $remainingMinutes > 0 
                ? $hours . 'h ' . $remainingMinutes . 'm'
                : $hours . ' hours';
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        return $remainingHours > 0 
            ? $days . 'd ' . $remainingHours . 'h'
            : $days . ' days';
    }
}
