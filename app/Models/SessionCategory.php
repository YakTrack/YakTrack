<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionCategory extends Model
{
    /** @use HasFactory<\Database\Factories\SessionCategoryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * @return HasMany<Session, $this>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }
}
