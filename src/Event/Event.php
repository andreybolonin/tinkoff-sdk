<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Event;

use League\Event\AbstractEvent;
use Psr\Http\Message\RequestInterface;

abstract class Event extends AbstractEvent
{
    /**
     * @var RequestInterface
     */
    public $request;

    /**
     * @var float
     */
    public $startTime;

    /**
     * @var float
     */
    public $endTime;

    /**
     * Create a new instance.
     *
     * @param RequestInterface  $request   The request instance.
     * @param float             $startTime The timestamp of the start of the request, with microsecond precision.
     * @param float             $endTime   The timestamp of the end of the request, with microsecond precision.
     */
    public function __construct(RequestInterface $request, float $startTime, float $endTime)
    {
        $this->request = $request;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    /**
     * Get total time.
     *
     * @return float
     */
    public function time(): float
    {
        return $this->endTime - $this->startTime;
    }
}
