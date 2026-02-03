<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CartItemsTableTest extends TestCase
{
    private string $table = 'cart_items';
    private array $expectedColumns = [
        'id',
        'cart_id',
        'product_id',
        'pcs',
        'created_at',
        'updated_at',
    ];

    public function test_cart_items_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
    }

    public function test_cart_items_table_has_expected_columns(): void
    {
        foreach ($this->expectedColumns as $col) {
            $this->assertTrue(Schema::hasColumn($this->table, $col), "Hiányzó oszlop: {$col}");
        }
    }
}
