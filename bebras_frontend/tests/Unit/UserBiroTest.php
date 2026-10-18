<?php

namespace Tests\Unit;

use App\Models\AnggotaBiro;
use App\Models\KoordinatorBiro;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserBiroTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_fillable_attributes_include_kode_biro(): void
    {
        $user = new User();
        $this->assertContains('kode_biro', $user->getFillable(), 'User fillable must contain kode_biro');
    }

    public function test_user_roles_relation_definition(): void
    {
        $user = new User();
        $relation = $user->roles();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Role::class, $relation->getRelated());
        $this->assertEquals('role_user', $relation->getTable());
    }

    public function test_user_koordinator_biro_relation_definition(): void
    {
        $user = new User();
        $relation = $user->koordinatorBiro();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertInstanceOf(KoordinatorBiro::class, $relation->getRelated());
        $this->assertEquals('user_id', $relation->getForeignKeyName());
    }

    public function test_user_anggota_biro_relation_definition(): void
    {
        $user = new User();
        $relation = $user->anggotaBiro();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertInstanceOf(AnggotaBiro::class, $relation->getRelated());
        $this->assertEquals('biro_id', $relation->getForeignKeyName());
    }

    public function test_user_has_role_method_returns_correct_boolean(): void
    {
        $biroRole = Role::firstOrCreate(['name' => 'biro']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $user = User::create([
            'name'      => 'Unit Test Biro Frontend',
            'username'  => 'unit_biro_fe_' . uniqid(),
            'email'     => 'unit_biro_fe_' . uniqid() . '@example.com',
            'password'  => bcrypt('secret'),
            'kode_biro' => '998',
        ]);
        $user->roles()->attach($biroRole->id);

        $this->assertTrue($user->hasRole('biro'));
        $this->assertFalse($user->hasRole('user'));
    }
}
