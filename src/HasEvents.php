<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK;

use League\Event\EmitterInterface;

trait HasEvents
{
    /**
     * @var EmitterInterface
     */
    private $emitter;

    /**
     * Get the event emitter instance.
     *
     * @return EmitterInterface|null
     */
    public function getEventEmitter()
    {
        return $this->emitter;
    }

    /**
     * Set the event emitter instance.
     *
     * @param EmitterInterface $emitter
     */
    public function setEventEmitter(EmitterInterface $emitter)
    {
        $this->emitter = $emitter;
    }

    /**
     * Unset the event emitter.
     */
    public function unsetEventEmitter()
    {
        $this->emitter = null;
    }

    /**
     * Emit an event.
     *
     * @param  string  $eventClassName
     * @param  array   $payload
     */
    protected function fire(string $eventClassName, array $payload)
    {
        if ($this->emitter) {
            $this->emitter->emit(new $eventClassName(...$payload));
        }
    }
}
