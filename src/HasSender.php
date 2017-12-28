<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK;

use Personnage\Tinkoff\SDK\Event\FailureEvent;
use Personnage\Tinkoff\SDK\Event\StartedEvent;
use Personnage\Tinkoff\SDK\Event\SuccessEvent;
use Personnage\Tinkoff\SDK\Exception\HttpException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait HasSender
{
    use HasEvents;

    /**
     * @var Sender|null
     */
    private $sender;

    public function setSender(Sender $sender)
    {
        $this->sender = $sender;
    }

    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Call http request and throw events.
     *
     * @param  RequestInterface $request
     *
     * @return ResponseInterface
     * @throws HttpException
     */
    protected function send(RequestInterface $request): ResponseInterface
    {
        $started = microtime(true);

        try {
            $this->fire(StartedEvent::class, [$request, $started]);
            $response = $this->sender->send($request);
            $this->fire(SuccessEvent::class, [$request, $response, $started, microtime(true)]);

            return $response;
        } catch (HttpException $e) {
            $this->fire(FailureEvent::class, [$request, $e, $started, microtime(true)]);

            throw $e;
        }
    }
}
