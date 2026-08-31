<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int|null $focused_client_id
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'focused_client_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The client the user is currently focused on, if any.
     *
     * @return BelongsTo<Client, $this>
     */
    public function focusedClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'focused_client_id');
    }

    /**
     * The id of the client the user is currently focused on, or null for all clients.
     */
    public function focusedClientId(): ?int
    {
        return $this->focused_client_id;
    }
}
