<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DatabaseDependencyService
{
    /**
     * Get all tables in the current database, excluding system tables.
     */
    public function getAllTables(): array
    {
        $tables = DB::select("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_TYPE = 'BASE TABLE'
            ORDER BY TABLE_NAME
        ");

        $excludedTables = [
            'migrations',
            'migration_batches',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'sessions',
            'password_reset_tokens',
            'password_resets',
            'telescope_entries',
            'telescope_entries_tags',
            'telescope_monitoring',
            'firewall_ips',
            'firewall_logs',
            'personal_access_tokens',
        ];

        return collect($tables)
            ->map(fn($t) => $t->TABLE_NAME)
            ->reject(fn($table) => in_array($table, $excludedTables))
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Get the parent tables (tables that this table depends on via foreign keys).
     */
    public function getParentTables(string $table): array
    {
        $foreignKeys = DB::select("
            SELECT 
                kcu.TABLE_NAME AS child_table,
                kcu.COLUMN_NAME AS child_column,
                kcu.REFERENCED_TABLE_NAME AS parent_table,
                kcu.REFERENCED_COLUMN_NAME AS parent_column
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu
            WHERE kcu.TABLE_SCHEMA = DATABASE()
            AND kcu.TABLE_NAME = ?
            AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
        ", [$table]);

        return collect($foreignKeys)
            ->pluck('parent_table')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Get the child tables (tables that depend on this table via foreign keys).
     */
    public function getChildTables(string $table): array
    {
        $foreignKeys = DB::select("
            SELECT 
                kcu.TABLE_NAME AS child_table,
                kcu.COLUMN_NAME AS child_column,
                kcu.REFERENCED_TABLE_NAME AS parent_table,
                kcu.REFERENCED_COLUMN_NAME AS parent_column
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu
            WHERE kcu.TABLE_SCHEMA = DATABASE()
            AND kcu.REFERENCED_TABLE_NAME = ?
            AND kcu.REFERENCED_COLUMN_NAME IS NOT NULL
        ", [$table]);

        return collect($foreignKeys)
            ->map(fn($fk) => (object) [
                'table' => $fk->child_table,
                'column' => $fk->child_column,
                'parent_column' => $fk->parent_column,
            ])
            ->values()
            ->toArray();
    }

    /**
     * Recursively get all ancestor tables (parents, grandparents, etc.).
     */
    public function getAllParentTables(string $table): array
    {
        $parents = [];
        $this->collectParents($table, $parents);

        return array_values(array_unique($parents));
    }

    /**
     * Recursively get all descendant tables (children, grandchildren, etc.).
     */
    public function getAllChildTables(string $table): array
    {
        $children = [];
        $this->collectChildren($table, $children);

        return array_values(array_unique($children));
    }

    /**
     * Get the row count for a table.
     */
    public function getTableRowCount(string $table): int
    {
        return DB::selectOne("SELECT COUNT(*) AS count FROM `{$table}`")->count;
    }

    /**
     * Get table details including row count for multiple tables.
     */
    public function getTablesInfo(array $tables): array
    {
        return collect($tables)->mapWithKeys(function ($table) {
            try {
                $count = $this->getTableRowCount($table);
                return [$table => ['name' => $table, 'count' => $count]];
            } catch (\Exception $e) {
                return [$table => ['name' => $table, 'count' => 0]];
            }
        })->toArray();
    }

    /**
     * Get the full dependency tree for a table.
     */
    public function getDependencyTree(string $table): array
    {
        return [
            'table' => $table,
            'parents' => $this->getAllParentTables($table),
            'children' => $this->getAllChildTables($table),
            'direct_parents' => $this->getParentTables($table),
            'direct_children' => $this->getChildTables($table),
        ];
    }

    /**
     * Generate a SQL dump for selected tables with proper ordering.
     */
    public function generateBackupSql(array $tables): string
    {
        $orderedTables = $this->orderTablesForInsert($tables);
        $sql = "-- All Academies Database Backup\n";
        $sql .= "-- Generated: " . now()->toDateTimeString() . "\n";
        $sql .= "-- Tables: " . implode(', ', $orderedTables) . "\n\n";

        foreach ($orderedTables as $table) {
            $sql .= $this->dumpTable($table);
        }

        return $sql;
    }

    /**
     * Order tables so parents come before children.
     */
    public function orderTablesForInsert(array $tables): array
    {
        $ordered = [];
        $visited = [];

        $visit = function ($table) use (&$visit, &$ordered, &$visited, $tables) {
            if (in_array($table, $visited)) {
                return;
            }
            $visited[] = $table;

            // Add parents first
            foreach ($this->getParentTables($table) as $parent) {
                if (in_array($parent, $tables)) {
                    $visit($parent);
                }
            }

            $ordered[] = $table;
        };

        foreach ($tables as $table) {
            $visit($table);
        }

        return $ordered;
    }

    /**
     * Dump a single table's structure and data.
     */
    protected function dumpTable(string $table): string
    {
        $sql = "--\n-- Table structure for table `{$table}`\n--\n\n";
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $sql .= "CREATE TABLE `{$table}` (\n";

        // Get column definitions
        $columns = DB::select("
            SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA, COLUMN_KEY
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
            ORDER BY ORDINAL_POSITION
        ", [$table]);

        $columnDefs = [];
        foreach ($columns as $column) {
            $def = "  `{$column->COLUMN_NAME}` {$column->COLUMN_TYPE}";
            $def .= $column->IS_NULLABLE === 'NO' ? ' NOT NULL' : '';
            if ($column->COLUMN_DEFAULT !== null) {
                $def .= " DEFAULT " . $this->quoteValue($column->COLUMN_DEFAULT);
            } elseif ($column->IS_NULLABLE === 'YES') {
                $def .= " DEFAULT NULL";
            }
            if ($column->EXTRA) {
                $def .= " {$column->EXTRA}";
            }
            $columnDefs[] = $def;
        }

        $sql .= implode(",\n", $columnDefs) . "\n";
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

        // Get foreign key constraints
        $foreignKeys = DB::select("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$table]);

        // Get primary key
        $primaryKey = DB::selectOne("
            SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND CONSTRAINT_NAME = 'PRIMARY'
        ", [$table]);

        if ($primaryKey) {
            $sql .= "--\n-- Data for table `{$table}`\n--\n\n";

            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $values = [];
                $columns = array_keys((array) $row);
                foreach ($columns as $col) {
                    $val = $row->{$col};
                    $values[] = $this->quoteValue($val);
                }
                $sql .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
            }
        }

        $sql .= "\n";

        // Add foreign key constraints
        foreach ($foreignKeys as $fk) {
            if (in_array($fk->REFERENCED_TABLE_NAME, $this->orderTablesForInsert($this->getAllTables()))) {
                $sql .= "ALTER TABLE `{$table}`\n";
                $sql .= "  ADD CONSTRAINT `{$fk->CONSTRAINT_NAME}`\n";
                $sql .= "  FOREIGN KEY (`{$fk->COLUMN_NAME}`)\n";
                $sql .= "  REFERENCES `{$fk->REFERENCED_TABLE_NAME}` (`{$fk->REFERENCED_COLUMN_NAME}`)\n";
                $sql .= "  ON DELETE CASCADE ON UPDATE CASCADE;\n\n";
            }
        }

        return $sql;
    }

    /**
     * Quote a value for SQL.
     */
    protected function quoteValue($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        return "'" . addslashes((string) $value) . "'";
    }

    /**
     * Recursively collect parent tables.
     */
    protected function collectParents(string $table, array &$parents, array $visited = []): void
    {
        if (in_array($table, $visited)) {
            return;
        }
        $visited[] = $table;

        foreach ($this->getParentTables($table) as $parent) {
            $parents[] = $parent;
            $this->collectParents($parent, $parents, $visited);
        }
    }

    /**
     * Recursively collect child tables.
     */
    protected function collectChildren(string $table, array &$children, array $visited = []): void
    {
        if (in_array($table, $visited)) {
            return;
        }
        $visited[] = $table;

        foreach ($this->getChildTables($table) as $child) {
            $children[] = $child->table;
            $this->collectChildren($child->table, $children, $visited);
        }
    }
}
