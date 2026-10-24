<?php

namespace Tests\Unit;

use App\Models\KoordinatorBiro;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class KoordinatorBiroTest extends TestCase
{
    public function test_koordinator_biro_table_name(): void
    {
        $koordinator = new KoordinatorBiro();
        $this->assertEquals('koordinator_biro', $koordinator->getTable());
    }

    public function test_koordinator_biro_fillable_attributes(): void
    {
        $koordinator = new KoordinatorBiro();
        $expectedFillables = ['user_id', 'nama', 'email', 'username', 'password', 'telepon'];

        $this->assertEquals($expectedFillables, $koordinator->getFillable());
    }

    public function test_koordinator_biro_user_relation_definition(): void
    {
        $koordinator = new KoordinatorBiro();
        $relation = $koordinator->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(User::class, $relation->getRelated());
        $this->assertEquals('user_id', $relation->getForeignKeyName());
    }

    public function test_koordinator_biro_biro_relation_definition(): void
    {
        $koordinator = new KoordinatorBiro();
        $relation = $koordinator->biro();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(User::class, $relation->getRelated());
        $this->assertEquals('user_id', $relation->getForeignKeyName());
    }
}
