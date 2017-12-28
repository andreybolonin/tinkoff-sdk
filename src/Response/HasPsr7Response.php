<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Response;

use Psr\Http\Message\ResponseInterface;

trait HasPsr7Response
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Get a current response instance.
     *
     * @return ResponseInterface
     */
    public function getPsr7Response(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Set a new response instance.
     *
     * @param ResponseInterface $response
     */
    protected function setPsr7Response(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
