<?php

namespace App\Traits;

use InvalidArgumentException;
use RuntimeException;

/**
 * Trait CanGetTableNameStatically
 *
 * Provides the ability to get the table name of an Eloquent model statically.
 * This trait should only be used with Eloquent models that implement getTable().
 *
 * @method string getTable() Method required from Laravel's Model class
 */
trait CanGetTableNameStatically
{
    /**
     * Get the model's table name statically
     *
     * @throws RuntimeException If the class doesn't implement getTable method
     * @throws InvalidArgumentException If the table name is empty
     * @return string The name of the database table
     */
    public static function getTableName(): string
    {
        $instance = new static();

        if (!method_exists($instance, 'getTable')) {
            throw new RuntimeException(
                sprintf('Class %s must implement getTable() method', static::class)
            );
        }

        $tableName = $instance->getTable();

        if (empty($tableName)) {
            throw new InvalidArgumentException(
                sprintf('Table name for %s cannot be empty', static::class)
            );
        }

        return $tableName;
    }

    /**
     * Check if a given table name matches this model's table
     *
     * @param string $tableName The table name to compare
     * @return bool Whether the table names match
     */
    public static function isTable(string $tableName): bool
    {
        return static::getTableName() === $tableName;
    }

    /**
     * Get the fully qualified table name including any set prefix
     *
     * @return string The fully qualified table name
     */
    public static function getFullTableName(): string
    {
        $instance = new static();
        return $instance->getConnection()->getTablePrefix() . static::getTableName();
    }
}
