<?php

namespace Tests\Functional;

use App\User;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    /**
     * Administrator home URL.
     *
     * @var string
     */
    protected $adminHomeUrl = '/home';

    /**
     * User account URL.
     *
     * @var string
     */
    protected $userAccountUrl = '/cuenta';

    /**
     * Login URL.
     *
     * @var string
     */
    protected $loginUrl = '/login';

    /**
     * Logout URL.
     *
     * @var string
     */
    protected $logoutUrl = '/logout';

    /**
     * test user login ok and redirect to user account URL.
     */
    public function testUserLoginOk()
    {
        // Prepare
        $user = factory(User::class)->create([
            'name' => 'Alice',
            'password' => bcrypt('123456'),
        ]);

        // Request
        $response = $this->call('POST', $this->loginUrl, [
            'email' => $user->email,
            'password' => '123456',
        ]);

        // Asserts
        $response->assertStatus(302);
        $response->assertRedirect($this->userAccountUrl);
        $response->assertLoggedUserIs($user);
    }

    /**
     * Test user can not login with wrong credentials.
     *
     * @dataProvider getInvalidLoginData
     * @param array $registrationData
     * @param array $sessionErrorKeys
     */
    public function testUserCanNotLoginWrongCredentialsError($registrationData, $sessionErrorKeys)
    {
        factory(User::class)->create([
            'name' => 'Alice',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->call('POST', $this->loginUrl, $registrationData);

        // If the user has the can_login field to false, we throw the error
        // saying that the credentials are wrong, and not the one saying that
        // the user is banned.
        $response->assertSessionHasErrors($sessionErrorKeys);
        $response->assertLoggedUserIs(null);
    }

    /**
     * Test redirect to homepage when a logged user does a logout
     */
    public function testUserLogoutRedirectToHomePage()
    {
        $user = factory(User::class)->create([
            'name' => 'Alice',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->actingAs($user)->call('GET', $this->logoutUrl);

        $response->assertRedirect('/');
        $response->assertLoggedUserIs(null);
    }

    /**
     * Test redirect to login if user is not authenticated and access to admin home URL
     *
     * // FALLA
     */
    public function testRedirectToLoginIfUserNotAuthenticatedAndAccessToAdminHomeUrl()
    {
        $response = $this->call('GET', $this->adminHomeUrl);
        $response->assertStatus(302);
    }

    /**
     * Test redirect to login if user is not authenticated and access to admin home URL
     */
    public function testRedirectToLoginIfUserNotAuthenticatedAndAccessToUserAccountUrl()
    {
        $response = $this->call('GET', $this->userAccountUrl);
        $response->assertRedirect($this->loginUrl);
    }

    /**
     * Returns an array with an example of invalid data.
     */
    public static function getInvalidLoginData()
    {
        return [
            [
                [   // Login data
                    'email' => '',
                    'password' => '123456',
                ],
                [   // Validation error keys
                    'email',
                ],
            ],
            [
                [   // Login data
                    'email' => 'aliasdomain.com',
                    'password' => '123',
                ],
                [   // Validation error keys
                    'email',
                ],
            ],
        ];
    }
}
