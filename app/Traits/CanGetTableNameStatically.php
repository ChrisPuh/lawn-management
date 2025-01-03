<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use RuntimeException;

/**
 * Provides static table name access for Eloquent models.
 *
 * @template TModel of Model
 */
trait CanGetTableNameStatically
{
    /**
     * Get the table name for the model.
     */
    public static function getTableName(): string
    {
        // Create a new instance of the model to access its table property
        $instance = self::createInstance();
        $tableName = $instance->getTable();

        // Ensure the table name is not empty
        if (empty($tableName)) {
            throw new InvalidArgumentException(
                sprintf('Table name for %s cannot be empty', static::class)
            );
        }

        return $tableName;
    }

    /**
     * Check if a given table name matches this model's table.
     *
     * @param  string  $tableName  The table name to compare.
     * @return bool Whether the table names match.
     */
    public static function isTable(string $tableName): bool
    {
        // Get the model's table name and compare it to the given table name
        return static::getTableName() === $tableName;
    }

    /**
     * Get the fully qualified table name including any set prefix.
     *
     * @return string The fully qualified table name.
     */
    public static function getFullTableName(): string
    {
        // Create a new instance of the model to access its connection
        $instance = self::createInstance();
        $tablePrefix = $instance->getConnection()->getTablePrefix();
        $tableName = static::getTableName();

        // Combine the table prefix and table name to get the fully qualified table name
        return $tablePrefix.$tableName;
    }

    /**
     * Create a new instance of the model.
     *
     * @throws RuntimeException
     */
    private static function createInstance(): Model
    {
        $calledClass = get_called_class();

        if (! is_a($calledClass, Model::class, true)) {
            throw new RuntimeException(
                sprintf('Class %s must extend %s', $calledClass, Model::class)
            );
        }

        return new $calledClass;
    }
}
