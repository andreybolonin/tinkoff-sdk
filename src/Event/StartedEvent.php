<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Event;

use Psr\Http\Message\RequestInterface;

final class StartedEvent extends Event
{
    /**
     * Create a new instance.
     *
     * @param RequestInterface  $request   The request instance.
     * @param float             $startTime The timestamp of the start of the request, with microsecond precision.
     */
    public function __construct(RequestInterface $request, float $startTime)
    {
        parent::__construct($request, $startTime, $startTime);
    }
}
