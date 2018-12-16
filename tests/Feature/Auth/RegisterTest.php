<?php
/**
 * Created by PhpStorm.
 * User: dominiksecka
 * Date: 2018-12-16
 * Time: 19:29.
 */

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_can_load_the_register_page()
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/register');

        $response->assertSuccessful();
    }

    /** @test */
    public function register_form_validator_should_not_fail_on_valid_input_data()
    {
        $validator = self::getMethod('validator');
        $obj = new RegisterController();
        $response = $validator
            ->invokeArgs($obj, [[
                'name'                  => 'dominik',
                'email'                 => 'secka.dominik@gmail.com',
                'password'              => 'password',
                'password_confirmation' => 'password',
            ]]);

        $this->assertFalse($response->fails());
    }

    /** @test */
    public function register_form_validator_should_fail_on_invalid_input_data()
    {
        $validator = self::getMethod('validator');
        $controller = new RegisterController();
        $response = $validator
            ->invokeArgs($controller, [[
                'name'                  => 'dominik',
                'email'                 => 'secka.dominik@gmail.com',
                'password'              => 'pas',
                'password_confirmation' => 'pas',
            ]]);

        $this->assertTrue($response->fails());
    }

    /** @test */
    public function register_new_user_with_input_data()
    {
        $createMethod = self::getMethod('create');
        $controller = new RegisterController();
        $createMethod->invokeArgs($controller, [[
            'name'     => 'dominik',
            'email'    => 'secka.dominik@gmail.com',
            'password' => 'password',
        ]]);

        $this->assertDatabaseHas('users', [
            'email' => 'secka.dominik@gmail.com',
        ]);
    }

    protected static function getMethod($name)
    {
        $class = new \ReflectionClass(RegisterController::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

}