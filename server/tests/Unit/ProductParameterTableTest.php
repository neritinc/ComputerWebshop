<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductParameterTableTest extends TestCase
{
    private string $table = 'product_parameter';
    private array $expectedColumns = [
        'id',
        'product_id',
        'parameter_id',
        'value',
        'created_at',
        'updated_at',
    ];

    public function test_product_parameter_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
    }

    public function test_product_parameter_table_has_expected_columns(): void
    {
        foreach ($this->expectedColumns as $col) {
            $this->assertTrue(Schema::hasColumn($this->table, $col), "Hiányzó oszlop: {$col}");
        }
    }
}

