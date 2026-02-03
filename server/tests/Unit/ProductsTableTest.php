<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductsTableTest extends TestCase
{
    private string $table = 'products';
    private array $expectedColumns = [
        'id',
        'category_id',
        'name',
        'pcs',
        'price',
        'description',
        'company_id',
    ];

    public function test_products_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
    }

    public function test_products_table_has_expected_columns(): void
    {
        foreach ($this->expectedColumns as $col) {
            $this->assertTrue(Schema::hasColumn($this->table, $col), "Hiányzó oszlop: {$col}");
        }
    }
}

