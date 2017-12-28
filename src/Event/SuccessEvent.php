<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Event;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class SuccessEvent extends Event
{
    /**
     * @var ResponseInterface
     */
    public $response;

    /**
     * Create a new instance.
     *
     * @param RequestInterface   $request   The request instance.
     * @param ResponseInterface  $response  The response instance.
     * @param float              $startTime The timestamp of the start of the request, with microsecond precision.
     * @param float              $endTime   The timestamp of the end of the request, with microsecond precision.
     */
    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        float $startTime,
        float $endTime
    ) {
        $this->response = $response;

        parent::__construct($request, $startTime, $endTime);
    }
}
