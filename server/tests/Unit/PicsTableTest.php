<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PicsTableTest extends TestCase
{
    private string $table = 'pics';
    private array $expectedColumns = [
        'id',
        'product_id',
        'image_path',
        'created_at',
        'updated_at',
    ];

    public function test_pics_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
    }

    public function test_pics_table_has_expected_columns(): void
    {
        foreach ($this->expectedColumns as $col) {
            $this->assertTrue(Schema::hasColumn($this->table, $col), "Hiányzó oszlop: {$col}");
        }
    }
}

