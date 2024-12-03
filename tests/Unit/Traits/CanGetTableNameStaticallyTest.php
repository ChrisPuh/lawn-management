<?php

namespace Tests\Unit\Traits;

use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CanGetTableNameStaticallyTest extends TestCase
{
    /**
     * Test valid model returns correct table name
     */
    public function test_get_table_name_returns_correct_name(): void
    {
        $tableName = ValidTestModel::getTableName();
        $this->assertEquals('test_table', $tableName);
    }

    /**
     * Test invalid model throws exception
     */
    public function test_get_table_name_throws_exception_for_invalid_model(): void
    {
        $this->expectException(RuntimeException::class);
        InvalidTestModel::getTableName();
    }

    /**
     * Test empty table name throws exception
     */
    public function test_get_table_name_throws_exception_for_empty_table(): void
    {
        $this->expectException(InvalidArgumentException::class);
        EmptyTableModel::getTableName();
    }

    /**
     * Test isTable method returns correct comparison
     */
    public function test_is_table_returns_correct_comparison(): void
    {
        $this->assertTrue(ValidTestModel::isTable('test_table'));
        $this->assertFalse(ValidTestModel::isTable('wrong_table'));
    }

    /**
     * Test getFullTableName returns prefixed name
     */
    public function test_get_full_table_name_returns_prefixed_name(): void
    {
        $fullTableName = PrefixedTestModel::getFullTableName();
        $this->assertEquals('prefix_test_table', $fullTableName);
    }
}

// Test Models
class ValidTestModel extends Model
{
    use CanGetTableNameStatically;

    protected $table = 'test_table';
}

class InvalidTestModel
{
    use CanGetTableNameStatically;
}

class EmptyTableModel extends Model
{
    use CanGetTableNameStatically;

    protected $table = '';
}

class PrefixedTestModel extends Model
{
    use CanGetTableNameStatically;

    protected $table = 'test_table';

    public function getConnection()
    {
        return new class {
            public function getTablePrefix()
            {
                return 'prefix_';
            }
        };
    }
}
