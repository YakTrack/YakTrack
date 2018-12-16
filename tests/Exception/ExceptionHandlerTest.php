<?php
/**
 * Created by PhpStorm.
 * User: dominiksecka
 * Date: 2018-12-15
 * Time: 15:37.
 */

namespace Tests\Exception;

use App\Exceptions\Handler;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    /** @test
     * @throws \ReflectionException
     */
    public function exception_handler_render_token_mismatch()
    {
        $request = $this->createMock(Request::class);
        $instance = new Handler($this->createMock(Container::class));
        $class = new \ReflectionClass(Handler::class);
        $method = $class->getMethod('render');
        $method->setAccessible(true);
        $expectedInstance = Response::class;
        $this->assertInstanceOf($expectedInstance, $method->invokeArgs($instance, [$request, $this->createMock(TokenMismatchException::class)]));
    }

    /** @test
     * @throws \ReflectionException
     */
    public function exception_handler_report_token_mismatch()
    {
        $instance = new Handler($this->createMock(Container::class));
        $class = new \ReflectionClass(Handler::class);
        $method = $class->getMethod('report');
        $method->setAccessible(true);
        $expected = null;
        $this->assertTrue($expected == $method->invokeArgs($instance, [$this->createMock(TokenMismatchException::class)]));
    }
    
}