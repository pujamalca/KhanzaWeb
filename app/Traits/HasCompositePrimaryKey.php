<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasCompositePrimaryKey
{
    /**
     * Set keys for save query (override default Eloquent behavior)
     */
    protected function setKeysForSaveQuery($query)
    {
        foreach ((array) $this->getPrimaryKeys() as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }

    /**
     * Get composite primary keys
     */
    public function getPrimaryKeys(): array
    {
        return ['nip', 'tgl_login', 'jam_login'];
    }

    /**
     * Override getKeyName() to return custom composite key
     */
    public function getKeyName(): string
    {
        return 'custom_key';
    }

    /**
     * Override getIncrementing() to always return false
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Override getKeyType() to always return string
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
