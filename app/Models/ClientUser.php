<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ClientUser extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
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
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the client that this user belongs to.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get all projects for this client user.
     */
    public function projects()
    {
        return $this->client->projects();
    }

    /**
     * Get all tasks for this client user's projects.
     */
    public function tasks()
    {
        return Task::whereHas('project', function ($query) {
            $query->where('client_id', $this->client_id);
        });
    }

    /**
     * Get all billable sessions for this client user's tasks.
     */
    public function billableSessions()
    {
        return Session::whereHas('task.project', function ($query) {
            $query->where('client_id', $this->client_id);
        })->whereBillable();
    }
}
