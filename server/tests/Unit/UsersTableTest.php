<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UsersTableTest extends TestCase
{
    private string $table = 'users';

    private array $expectedColumns = [
        'id',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'city',
        'street',
        'house_number',
        'zip_code',
        'billing_phone',
        'billing_city',
        'billing_street',
        'billing_house_number',
        'billing_zip_code',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    public function test_users_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table), 'users tábla nem létezik');
    }

    public function test_users_table_has_expected_columns(): void
    {
        foreach ($this->expectedColumns as $col) {
            $this->assertTrue(
                Schema::hasColumn($this->table, $col),
                "Hiányzó oszlop a users táblában: {$col}"
            );
        }
    }
}

