<?php

namespace App\Models\Concerns;

use App\Models\Client;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToClient
{
    use HasRelations;

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getClient(): Client
    {
        return $this->getRelation(Client::class);
    }
}
