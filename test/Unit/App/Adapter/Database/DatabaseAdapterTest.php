<?php

declare(strict_types=1);

namespace Unit\App\Adapter\Database;

use App\Adapter\Database\DatabaseAdapter;
use Hyperf\DbConnection\Db;
use Mockery as m;
use Unit\TestCase;


class DatabaseAdapterTest extends TestCase
{
    public function testShouldSelectOne(): void
    {
        // arrange
        $db = m::mock(Db::class);
        $databaseAdapter = new DatabaseAdapter($db);
        $query = 'SELECT';
        $bindings = [
            'id' => 1,
        ];
        $resultDatabase = (object) [
            'id' => 1,
        ];

        $db->shouldReceive('selectOne')->with($query, $bindings, true)->andReturn($resultDatabase);

        // act
        $result = $databaseAdapter->selectOne($query, $bindings);

        // assert
        $this->assertSame($resultDatabase, $result);
    }

    public function testShouldSelect(): void
    {
        // arrange
        $db = m::mock(Db::class);
        $databaseAdapter = new DatabaseAdapter($db);
        $query = 'SELECT';
        $bindings = [
            'id' => 1,
        ];
        $resultDatabase = [
            (object) [
                'id' => 1,
            ],
        ];

        $db->shouldReceive('select')->with($query, $bindings, true)->andReturn($resultDatabase);

        // act
        $result = $databaseAdapter->select($query, $bindings);

        // assert
        $this->assertSame($resultDatabase, $result);
    }

    public function testShouldInsert(): void
    {
        // arrange
        $db = m::mock(Db::class);
        $databaseAdapter = new DatabaseAdapter($db);
        $query = 'INSERT';
        $bindings = [
            'id' => 1,
        ];

        $db->shouldReceive('insert')->with($query, $bindings)->andReturnTrue();

        // act
        $result = $databaseAdapter->insert($query, $bindings);

        // assert
        $this->assertTrue($result);
    }

    public function testShouldUpdate(): void
    {
        // arrange
        $db = m::mock(Db::class);
        $databaseAdapter = new DatabaseAdapter($db);
        $query = 'UPDATE';
        $bindings = [
            'id' => 1,
        ];

        $db->shouldReceive('update')->with($query, $bindings)->andReturn(1);

        // act
        $result = $databaseAdapter->update($query, $bindings);

        // assert
        $this->assertSame(1, $result);
    }

    public function testShouldBeginTransaction(): void
    {
        // arrange
        $db = m::mock(Db::class);
        $databaseAdapter = new DatabaseAdapter($db);

        $db->shouldReceive('beginTransaction');

        // act
        $result = $databaseAdapter->beginTransaction();

        // assert
        $this->assertNull($result);
    }

    public function testShouldCommitTransaction(): void
    {
        // arrange
        $db = m::mock(Db::class);
        $databaseAdapter = new DatabaseAdapter($db);

        $db->shouldReceive('commit');

        // act
        $result = $databaseAdapter->commit();

        // assert
        $this->assertNull($result);
    }

    public function testShouldRollbackTransaction(): void
    {
        // arrange
        $db = m::mock(Db::class);
        $databaseAdapter = new DatabaseAdapter($db);

        $db->shouldReceive('rollBack');

        // act
        $result = $databaseAdapter->rollBack();

        // assert
        $this->assertNull($result);
    }
}
