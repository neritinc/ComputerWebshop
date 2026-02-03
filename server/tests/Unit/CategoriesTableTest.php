<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CategoriesTableTest extends TestCase
{
    private string $table = 'categories';
    private array $expectedColumns = [
        'id',
        'category_name',
        'created_at',
        'updated_at',
    ];

    public function test_categories_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
    }

    public function test_categories_table_has_expected_columns(): void
    {
        foreach ($this->expectedColumns as $col) {
            $this->assertTrue(Schema::hasColumn($this->table, $col), "Hiányzó oszlop: {$col}");
        }
    }
}

