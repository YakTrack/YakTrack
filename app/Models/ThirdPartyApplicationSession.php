<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThirdPartyApplicationSession extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<Session, ThirdPartyApplicationSession>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function isForThirdPartyApplication(ThirdPartyApplication $app): bool
    {
        return $this->third_party_application_id === $app->id;
    }
}
