<?php

namespace App\Models\Concerns;

use App\Models\Client;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToClient
{
    use HasRelations;

    public function client(): BelongsTo
    {
        if ($this->parent) {
            return $this->parent->client();
        }

        return $this->belongsTo(Client::class);
    }

    public function getClient(): Client
    {
        return $this->getRelation(Client::class);
    }
}
