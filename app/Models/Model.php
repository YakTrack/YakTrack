<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    public function __construct($params = [])
    {
        parent::__construct($params);

        $this->appends = array_merge($this->appends, [
            'editUrl',
            'showUrl',
        ]);
    }

    public function getEditUrlAttribute()
    {
        return route($this->resourceName().'.edit', [
            $this->resourceName() => $this,
        ]);
    }

    public function getShowUrlAttribute()
    {
        return route($this->resourceName().'.show', [
            $this->resourceName() => $this,
        ]);
    }

    protected function resourceName()
    {
        return strtolower(class_basename(static::class));
    }
}
