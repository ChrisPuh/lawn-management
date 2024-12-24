<?php

declare(strict_types=1);

namespace Tests\Unit\Traits;

use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class CanGetTableNameStaticallyTest extends TestCase
{
    public function test_get_table_name_returns_properly(): void
    {
        $this->assertEquals('test_table', ValidTestModel::getTableName());
    }

    public function test_get_table_name_throws_exception_for_non_model(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Class '.InvalidTestModel::class.' must extend '.Model::class);
        InvalidTestModel::getTableName();
    }

    public function test_get_table_name_throws_exception_for_empty_table(): void
    {
        $this->expectException(InvalidArgumentException::class);
        EmptyTableModel::getTableName();
    }

    public function test_is_table_returns_boolean_correctly(): void
    {
        $this->assertTrue(ValidTestModel::isTable('test_table'));
        $this->assertFalse(ValidTestModel::isTable('wrong_table'));
    }

    public function test_get_full_table_name_returns_prefixed_name(): void
    {
        $this->assertEquals('prefix_test_table', PrefixedTestModel::getFullTableName());
    }
}

final class ValidTestModel extends Model
{
    use CanGetTableNameStatically;

    protected $table = 'test_table';
}

final class InvalidTestModel
{
    use CanGetTableNameStatically;
}

final class EmptyTableModel extends Model
{
    use CanGetTableNameStatically;

    protected $table = '';
}

final class PrefixedTestModel extends Model
{
    use CanGetTableNameStatically;

    protected $table = 'test_table';

    public function getConnection(): Connection
    {
        return new TestConnection;
    }
}

final class TestConnection extends Connection
{
    public function __construct()
    {
        parent::__construct(function () {}, '');
    }

    public function getTablePrefix(): string
    {
        return 'prefix_';
    }
}
