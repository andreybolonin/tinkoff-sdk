<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Personnage\Tinkoff\SDK\Exception\HttpException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait GuzzleSender
{
    /**
     * Send a request.
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     *
     * @throws \Personnage\Tinkoff\SDK\Exception\HttpException
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        static $http = null;

        if (null === $http) {
            $http = new HttpClient();
        }

        try {
            return $http->send($request, $this->useOptions());
        } catch (GuzzleException $e) {
            throw new HttpException('Http error', 0, $e);
        }
    }

    /**
     * Request options control various aspects of a request including, headers,
     * query string parameters, timeout settings, the body of a request, and much more.
     *
     * @return array
     *
     * @link http://docs.guzzlephp.org/en/stable/request-options.html
     */
    protected function useOptions(): array
    {
        return [];
    }
}
