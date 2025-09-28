<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskStatus extends Model
{
    /** @use HasFactory<\Database\Factories\TaskStatusFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'sort_order',
        'is_default',
        'is_completed',
        'project_id',
    ];

    protected $casts = [
        'is_default'   => 'boolean',
        'is_completed' => 'boolean',
    ];

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'status_id');
    }
}
