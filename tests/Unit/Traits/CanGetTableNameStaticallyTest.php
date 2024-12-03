<?php

namespace Tests\Unit\Traits;

use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CanGetTableNameStaticallyTest extends TestCase
{
    public function testGetTableNameReturnsProperly(): void
    {
        $this->assertEquals('test_table', ValidTestModel::getTableName());
    }

    public function testGetTableNameThrowsExceptionForNonModel(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Class ' . InvalidTestModel::class . ' must extend ' . Model::class);
        InvalidTestModel::getTableName();
    }

    public function testGetTableNameThrowsExceptionForEmptyTable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        EmptyTableModel::getTableName();
    }

    public function testIsTableReturnsBooleanCorrectly(): void
    {
        $this->assertTrue(ValidTestModel::isTable('test_table'));
        $this->assertFalse(ValidTestModel::isTable('wrong_table'));
    }

    public function testGetFullTableNameReturnsPrefixedName(): void
    {
        $this->assertEquals('prefix_test_table', PrefixedTestModel::getFullTableName());
    }
}

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

    public function getConnection(): Connection
    {
        return new TestConnection();
    }
}

class TestConnection extends Connection
{
    public function __construct()
    {
        parent::__construct(function() {}, '');
    }

    public function getTablePrefix(): string
    {
        return 'prefix_';
    }
}
