<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectJiraIntegration extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectJiraIntegrationFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'site_host',
        'account_email',
        'api_token',
    ];

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'account_email' => 'encrypted',
            'api_token' => 'encrypted',
        ];
    }
}
