<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Event;

use Psr\Http\Message\RequestInterface;
use Personnage\Tinkoff\SDK\Exception\HttpException;

final class FailureEvent extends Event
{
    /**
     * @var HttpException
     */
    public $exception;

    /**
     * Create a new instance.
     *
     * @param RequestInterface  $request   The request instance.
     * @param HttpException     $exception
     * @param float             $startTime The timestamp of the start of the request, with microsecond precision.
     * @param float             $endTime   The timestamp of the end of the request, with microsecond precision.
     */
    public function __construct(
        RequestInterface $request,
        HttpException $exception,
        float $startTime,
        float $endTime
    ) {
        $this->exception = $exception;

        parent::__construct($request, $startTime, $endTime);
    }
}
