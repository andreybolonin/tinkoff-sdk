<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface Sender
{
    /**
     * Send a request.
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     *
     * @throws \Personnage\Tinkoff\SDK\Exception\HttpException
     */
    public function send(RequestInterface $request): ResponseInterface;
}
