<?php

namespace App\Models\Concerns;

trait HasRelations
{
    public function getRelation($className)
    {
        if (is_null($this->{$relationKey = strtolower(class_basename($className))})) {
            return new $className();
        }

        return $this->$relationKey;
    }
}
