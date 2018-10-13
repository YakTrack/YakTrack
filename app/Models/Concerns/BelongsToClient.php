<?php

namespace App\Models\Concerns;

use App\Models\Client;

trait BelongsToClient
{
    use HasRelations;

    public function client()
    {
        if ($this->parent) {
            return $this->parent->client();
        }

        return $this->belongsTo(Client::class);
    }

    public function getClient()
    {
        return $this->getRelation(Client::class);
    }
}
