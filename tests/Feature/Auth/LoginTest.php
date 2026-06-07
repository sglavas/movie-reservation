<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    private function generateRequestData(string $email, string $password): array
    {
        return[
            'email' => $email,
            'password' => $password
        ];
    }

    private function submitCredentialsAndAssertSuccess(array $requestData): void
    {
        /* ACT */
        $response = $this->from('login')
                         ->post('/login', $requestData);
        
        /* ASSERT */
        $this->assertAuthenticatedAs($this->user);
        $response->assertRedirect('/showtimes');
    }

    private function submitCredentialsAndAssertFailure(array $requestData, string $field): void
    {
        /* ACT */
        $response = $this->from('login')
                         ->post('/login', $requestData);

        /* ASSERT */
        $response->assertSessionHasErrors($field);
        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_user_can_authenticate_with_correct_credentials(): void
    {
        /* ARRANGE */
        $requestData = $this->generateRequestData($this->user->email, 'password');

        $this->submitCredentialsAndAssertSuccess($requestData);
    }

    /**
     * Incorrect Credentials Test
     * 
     * Laravel sends a default validation message if the password field is empty:
     * 
     *  'password' => 'The password field is required.'
     * 
     * However, for security reasons, the SessionController always sends the same error message regardless of whether the email or the password is incorrect:
     * 
     *  'email' => 'The provided credentials do not match our records.'
     *
     * @param string $field - The name of the input field
     * @param string $value - The value of the input field
     * @param string $errorKey - The error key
     * @return void
     */
    #[DataProvider('authorizationFailureDataProvider')]
    public function test_user_cannot_authenticate_with_incorrect_credentials(string $field, string $value, string $errorKey): void
    {
        /* ARRANGE */
        $requestData = $this->generateRequestData($this->user->email, 'password');
            
        $requestData[$field] = $value;

        $this->submitCredentialsAndAssertFailure($requestData, $errorKey);
    }

    #[DataProvider('validationFailureDataProvider')]
    public function test_user_cannot_authenticate_with_invalid_input(string $field, string|null $value): void
    {
        /* ARRANGE */
        $requestData = $this->generateRequestData($this->user->email, 'password');

        if(is_null($value)){
            unset($requestData[$field]);
        } else{
            $requestData[$field] = $value;
        }
            
        $this->submitCredentialsAndAssertFailure($requestData, $field);
    }

    public function test_rate_limiter_does_not_allow_more_than_five_invalid_login_attempts(): void
    {
        /* ARRANGE */
        $requestData = $this->generateRequestData($this->user->email, 'incorrectPassword');

        /* ACT */
        $i = 0;

        while($i <= 5) {
            $response = $this->from('login')
                            ->post('/login', $requestData);
            $i++;
        }

        /* ASSERT */
        $response->assertSessionHasErrors([
            'email' => 'Too many login attempts. Please try again in 1 minute.',
        ]);
        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_cannot_access_login_page(): void
    {
        /* ARRANGE */
        $user = User::factory()->create();
        $this->actingAs($user);
        
        /* ACT */
        $response = $this->get('/login');

        /* ASSERT */
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_user_cannot_submit_login_request(): void
    {
        /* ARRANGE */
        $user = User::factory()->create();
        $this->actingAs($user);

        /* ACT */
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        /* ASSERT */
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }


    public static function authorizationFailureDataProvider(): array
    {
        return [
            'incorrect email' => ['email', 'incorrect@email.com', 'email'],

            'incorrect password' => ['password', 'incorrectPassword', 'email'],
        ];
    }

    public static function validationFailureDataProvider(): array
    {
        return [
            'empty email' => ['email', ''],
            'missing email' => ['email', null],
            'invalid email format' => ['email', 'invalidEmailFormat'],

            'empty password' => ['password', ''],
            'missing password' => ['password', null],
        ];
    }
}
