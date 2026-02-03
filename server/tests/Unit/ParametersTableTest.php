<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ParametersTableTest extends TestCase
{
    private string $table = 'parameters';
    private array $expectedColumns = [
        'id',
        'parameter_name',
        'category_id',
        'unit_id',
        'created_at',
        'updated_at',
    ];

    public function test_parameters_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
    }

    public function test_parameters_table_has_expected_columns(): void
    {
        foreach ($this->expectedColumns as $col) {
            $this->assertTrue(Schema::hasColumn($this->table, $col), "Hiányzó oszlop: {$col}");
        }
    }
}

