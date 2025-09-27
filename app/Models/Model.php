<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->appends = array_merge($this->appends, [
            'editUrl',
            'showUrl',
            'destroyUrl',
        ]);
    }

    public function getEditUrlAttribute(): string
    {
        return route($this->resourceName().'.edit', [
            $this->resourceName() => $this,
        ]);
    }

    public function getShowUrlAttribute(): string
    {
        return route($this->resourceName().'.show', [
            $this->resourceName() => $this,

        ]);
    }

    public function getDestroyUrlAttribute(): string
    {
        return route($this->resourceName().'.destroy', [
            $this->resourceName() => $this,
        ]);
    }

    protected function resourceName(): string
    {
        return strtolower(class_basename(static::class));
    }
}
