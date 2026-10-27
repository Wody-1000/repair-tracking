<?php

namespace Tests\Feature;

use App\Http\Middleware\SetDatabaseContext;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role', 20)->default(User::ROLE_FRONT_DESK);
            $table->rememberToken();
            $table->timestamps();
        });

        $this->withoutMiddleware(SetDatabaseContext::class);
    }

    public function test_public_registration_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Unapproved user',
            'email' => 'unapproved@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'unapproved@example.com']);
    }

    public function test_guest_is_redirected_to_login_from_staff_account_creation(): void
    {
        $this->get(route('users.create'))->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_create_staff_accounts(): void
    {
        foreach ([User::ROLE_FRONT_DESK, User::ROLE_TECHNICIAN] as $role) {
            $user = $this->createUser($role, $role.'@example.com');

            $this->actingAs($user)
                ->get(route('users.create'))
                ->assertForbidden();

            $this->post(route('users.store'), [
                'name' => 'New employee',
                'email' => 'new-'.$role.'@example.com',
                'role' => User::ROLE_ADMIN,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])->assertForbidden();
        }

        $this->assertDatabaseMissing('users', ['email' => 'new-front_desk@example.com']);
        $this->assertDatabaseMissing('users', ['email' => 'new-technician@example.com']);
    }

    public function test_admin_can_create_staff_account_with_selected_role(): void
    {
        $admin = $this->createUser(User::ROLE_ADMIN, 'admin@example.com');

        $this->actingAs($admin)
            ->get(route('users.create'))
            ->assertOk()
            ->assertSee('Create staff account');

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Repair technician',
                'email' => 'technician@example.com',
                'role' => User::ROLE_TECHNICIAN,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success', 'User created.');

        $newUser = User::where('email', 'technician@example.com')->firstOrFail();

        $this->assertModelExists($newUser);
        $this->assertSame(User::ROLE_TECHNICIAN, $newUser->role);
        $this->assertTrue(Hash::check('password123', $newUser->password));
    }

    public function test_admin_cannot_assign_an_unknown_role(): void
    {
        $admin = $this->createUser(User::ROLE_ADMIN, 'admin@example.com');

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Unauthorized admin',
                'email' => 'unauthorized@example.com',
                'role' => 'super_admin',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'unauthorized@example.com']);
    }

    private function createUser(string $role, string $email): User
    {
        $user = new User([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => 'password123',
        ]);
        $user->role = $role;
        $user->save();

        return $user;
    }
}
