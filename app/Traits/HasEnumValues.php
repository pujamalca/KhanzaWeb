<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasEnumValues
{
    /**
     * Get ENUM values from a database column
     *
     * @param string $column The column name to get ENUM values from
     * @return array Array of ENUM values
     */
    public static function getEnumValues(string $column): array
    {
        $model = new static;
        $table = $model->getTable();
        $type = DB::select("SHOW COLUMNS FROM `{$table}` WHERE Field = '{$column}'")[0]->Type ?? '';

        // Extract values from ENUM definition: enum('value1','value2','value3')
        preg_match('/^enum\((.*)\)$/', $type, $matches);

        if (!isset($matches[1])) {
            return [];
        }

        // Split and clean the values
        $enum = [];
        foreach (explode(',', $matches[1]) as $value) {
            // Remove quotes from values
            $enum[] = trim($value, " '");
        }

        return $enum;
    }

    /**
     * Get ENUM values as options array for select fields
     *
     * @param string $column The column name
     * @return array Associative array [value => label]
     */
    public static function getEnumOptions(string $column): array
    {
        $values = static::getEnumValues($column);
        return array_combine($values, $values);
    }
}
