<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasCompositePrimaryKey
{
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }

    public function getKeyName()
{
    return is_array($this->primaryKey) ? implode('_', $this->primaryKey) : $this->primaryKey;
}



    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
