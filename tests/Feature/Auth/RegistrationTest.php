<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private array $request;
    protected function setUp(): void
    {
        parent::setUp();

        // Perfect base request
        $this->request = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->email(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
    }

    private function generateRequestData(string $name, string $surname, string $email, string $password, string $passwordConfirmation): array
    {
        return[
            'first_name' => $name,
            'last_name' => $surname,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ];
    }

    private function submitFormAndAssertFailure(string $errorKey): void
    {
        /* ACT */
        $response = $this->from('/register')
                         ->post('/register', $this->request);

        /* ASSERT */
        $this->assertDatabaseMissing(
            'users',
            [
                'first_name' => $this->request['first_name'],
                'last_name' => $this->request['last_name'],
                'email' => $this->request['email'],
            ]);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors($errorKey);
        $this->assertGuest();
    }

    public function test_user_can_register_with_valid_data(): void
    {
        /* ACT */
        $response = $this->from('/register')
                         ->post('/register', $this->request);

        /* ASSERT */
        $this->assertDatabaseHas(
            'users',
            [
                'first_name' => $this->request['first_name'],
                'last_name' => $this->request['last_name'],
                'email' => $this->request['email'],
            ]
        );
        $this->assertAuthenticated();
        $response->assertRedirect('/showtimes');
    }

    #[DataProvider('invalidDataProvider')]
    public function test_user_cannot_register_with_invalid_input(string $field, string|null $value): void
    {
        /* ARRANGE */
        if(is_null($value)){
            unset($this->request[$field]);
        }else{
            $this->request[$field] = $value;
        }

        /* ACT */
        $response = $this->from('/register')
                         ->post('/register', $this->request);

        /* ASSERT */
        $this->assertDatabaseEmpty('users');
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors($field);
    }

    public function test_user_cannot_register_with_mismatched_password_confirmation(): void
    {
        /* ARRANGE */
        $this->request['password_confirmation'] = 'mismatchedPassword';

        $this->submitFormAndAssertFailure('password');
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        /* ARRANGE */
        User::factory()->create([
            'email' => $this->request['email'],
        ]);

        $this->submitFormAndAssertFailure('email');
    }

    public function test_authenticated_user_cannot_access_registration_page(): void
    {
        /* ARRANGE */
        $user = User::factory()->create();
        $this->actingAs($user);
        
        /* ACT */
        $response = $this->get('/register');

        /* ASSERT */
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
    
    public function test_authenticated_user_cannot_submit_registration_request(): void
    {
        /* ARRANGE */
        $user = User::factory()->create();
        $this->actingAs($user);

        /* ACT */
        $response = $this->post('/register', []);

        /* ASSERT */
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public static function invalidDataProvider(): array
    {
        return [
            // First name
            'empty first name' => ['first_name', ''],
            'first name too short' => ['first_name', 'a'],
            'first name too long' => ['first_name', fake()->paragraphs(5, true)],
            'missing first name' => ['first_name', null],
            // Last name
            'empty last name' => ['last_name', ''],
            'last name too short' => ['last_name', 'a'],
            'last name too long' => ['last_name', fake()->paragraph(5, true)],
            'missing last name' => ['last_name', null],
            // Email
            'empty email' => ['email', ''],
            'invalid email' => ['email', 'invalidEmail'],
            'missing email' => ['email', null],
            // Password
            'empty password' => ['password', ''],
            'password too short' => ['password', 'pass'],
            'password too long' => ['password', fake()->paragraphs(5, true)],
            'missing password' => ['password', null],
        ];
    }
}
