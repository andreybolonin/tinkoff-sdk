<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Merchant;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Personnage\Tinkoff\SDK\Response\Init;
use Personnage\Tinkoff\SDK\Response\Response;
use Personnage\Tinkoff\SDK\Sender;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MerchantAPITest extends TestCase
{
    protected function createClient(ResponseInterface $response = null)
    {
        if (null === $response) {
            $response = $this->createPsr7Response();
        }

        return new Client('', '', '', new class($response) implements Sender {
            private $response;

            public function __construct($response)
            {
                $this->response = $response;
            }

            public function send(RequestInterface $request): ResponseInterface
            {
                return $this->response;
            }
        });
    }

    protected function createPsr7Response(array $body = [], array $headers = [], int $status = 200)
    {
        return new Psr7Response($status, $headers, json_encode($body));
    }

    public function testInitRequestWillReturnResponse()
    {
        $client = $this->createClient();
        $response = $client->init('SomeOrderId', 1000);

        $this->assertInstanceOf(Init::class, $response);
        $this->assertInstanceOf(Response::class, $response);
    }
}
