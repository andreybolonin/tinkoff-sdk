<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Response;

use GuzzleHttp\Psr7\Response;
use Personnage\Tinkoff\SDK\Exception\HasErrorException;
use Psr\Http\Message\ResponseInterface;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function createPsr7Response(array $body = [], array $headers = [], int $status = 200)
    {
        return new Response($status, $headers, json_encode($body));
    }

    public function testGetPsr7Response()
    {
        $response = new Init($this->createPsr7Response());
        $this->assertInstanceOf(ResponseInterface::class, $response->getPsr7Response());
    }

    public function testGetErrorCode()
    {
        $response = new Init($this->createPsr7Response(['ErrorCode' => '0']));
        $this->assertEquals(0, $response->getErrorCode());

        $response = new Init($this->createPsr7Response(['ErrorCode' => '9999']));
        $this->assertEquals(9999, $response->getErrorCode());
    }

    public function testGetErrorMessage()
    {
        $response = new Init($this->createPsr7Response());
        $this->assertEquals('No error', $response->getErrorMessage());

        $response = new Init($this->createPsr7Response(['Details' => 'Bad request']));
        $this->assertEquals('Bad request', $response->getErrorMessage());
    }

    public function testHasError()
    {
        $response = new Init($this->createPsr7Response(['ErrorCode' => '0']));
        $this->assertFalse($response->hasError());

        $response = new Init($this->createPsr7Response(['ErrorCode' => '9999']));
        $this->assertTrue($response->hasError());
    }

    public function testRaiseExceptionIfError()
    {
        $response = new Init($this->createPsr7Response(['ErrorCode' => '0']));
        $response->raiseIfError();

        $this->expectException(HasErrorException::class);
        $response = new Init($this->createPsr7Response(['ErrorCode' => '9999']));
        $response->raiseIfError();
    }
}
