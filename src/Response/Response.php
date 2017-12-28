<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Response;

use Personnage\Tinkoff\SDK\Exception\HasErrorException;
use function Personnage\Tinkoff\SDK\message_get_body;
use Psr\Http\Message\ResponseInterface;

abstract class Response
{
    private $errorCode;
    private $errorMessage;

    public function hasError(): bool
    {
        return $this->getErrorCode() !== 0;
    }

    public function getErrorCode(): int
    {
        if (null === $this->errorCode) {
            $body = message_get_body($this->getPsr7Response());
            $this->errorCode = (int) $body['ErrorCode'];
        }

        return $this->errorCode;
    }

    public function getErrorMessage(): string
    {
        if (null === $this->errorMessage) {
            $body = message_get_body($this->getPsr7Response());
            $this->errorMessage = $body['Details'] ?? 'No error';
        }

        return $this->errorMessage;
    }

    /**
     * Throws an exception if error.
     *
     * @throws HasErrorException
     */
    public function raiseIfError()
    {
        if ($this->hasError()) {
            throw new HasErrorException($this->getErrorMessage(), $this->getErrorCode());
        }
    }

    /**
     * Get PSR-7 response instance.
     *
     * @return ResponseInterface
     */
    abstract public function getPsr7Response(): ResponseInterface;
}
