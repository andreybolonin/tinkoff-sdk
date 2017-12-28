<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Exception;

use Personnage\Tinkoff\SDK\Response\Response;

class ResponseException extends \RuntimeException implements TinkoffSDKException
{
    /**
     * @var Response
     */
    private $response;

    public function __construct(Response $response)
    {
        parent::__construct($response->getErrorMessage(), $response->getErrorCode());

        $this->response = $response;
    }

    /**
     * Get a response instance.
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}
