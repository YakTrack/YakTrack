<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ClientUser extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\ClientUserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'client_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
    ];

    /**
     * Get the client that this user belongs to.
     */
    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get all projects for this client user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Project, \App\Models\Client>
     */
    public function projects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->client()->first()->projects();
    }

    /**
     * Get all tasks for this client user's projects.
     *
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Task>
     */
    public function tasks(): \Illuminate\Database\Eloquent\Builder
    {
        return Task::whereHas('project', function ($query) {
            $query->where('client_id', $this->client_id);
        });
    }

    /**
     * Get all billable sessions for this client user's tasks.
     *
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Session>
     */
    public function billableSessions(): \Illuminate\Database\Eloquent\Builder
    {
        return Session::whereHas('task.project', function ($query) {
            $query->where('client_id', $this->client_id);
        })->whereBillable();
    }

    /**
     * Get all login sessions for this client user.
     */
    public function loginSessions(): HasMany
    {
        return $this->hasMany(ClientLoginSession::class);
    }

    /**
     * Get active login sessions for this client user.
     */
    public function activeLoginSessions(): HasMany
    {
        return $this->loginSessions()->active();
    }

    /**
     * Get the latest login session for this client user.
     */
    public function latestLoginSession(): HasMany
    {
        return $this->loginSessions()->latest('logged_in_at');
    }

    /**
     * Log a new login session.
     */
    public function logLoginSession(array $metadata = []): ClientLoginSession
    {
        return $this->loginSessions()->create([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'logged_in_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Log out all active sessions for this client user.
     */
    public function logOutAllSessions(): void
    {
        $this->activeLoginSessions()->update([
            'logged_out_at' => now(),
            'is_active' => false,
        ]);
    }
}
