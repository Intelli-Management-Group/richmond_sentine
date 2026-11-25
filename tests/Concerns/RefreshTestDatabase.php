<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;
use RuntimeException;

/**
 * Gives each test an empty copy of the live schema and rolls back whatever the
 * test writes.
 *
 * The schema comes from tests/database/schema.sql (see dump-schema.php) and is
 * loaded once per test run. Live tables are MyISAM, which ignores transactions,
 * so the test copy is created as InnoDB to make the per-test rollback work.
 */
trait RefreshTestDatabase
{
    private static bool $testSchemaLoaded = false;

    protected function setUpRefreshTestDatabase(): void
    {
        $database = config('database.connections.mysql.database');

        if (! str_ends_with($database, '_testing')) {
            throw new RuntimeException("Refusing to reset '{$database}': the test database name must end in _testing (see phpunit.xml).");
        }

        if (! self::$testSchemaLoaded) {
            $this->loadTestSchema($database);
            self::$testSchemaLoaded = true;
        }

        $connection = DB::connection();
        $connection->beginTransaction();

        $this->beforeApplicationDestroyed(function () use ($connection) {
            $connection->rollBack();
            $connection->disconnect();
        });
    }

    private function loadTestSchema(string $database): void
    {
        $config = config('database.connections.mysql');

        $server = new PDO(
            "mysql:host={$config['host']};port={$config['port']}",
            $config['username'],
            $config['password'],
        );
        $server->exec("create database if not exists `{$database}` character set utf8mb4 collate utf8mb4_unicode_ci");

        DB::purge();
        Schema::dropAllTables();

        $schema = file_get_contents(base_path('tests/database/schema.sql'));
        DB::unprepared(str_replace('ENGINE=MyISAM', 'ENGINE=InnoDB', $schema));
    }
}
