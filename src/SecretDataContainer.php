<?php declare(strict_types = 1);

namespace Personnage\Tinkoff\SDK;

final class SecretDataContainer
{
    /**
     * @var string
     */
    private $serial;
    /**
     * @var string
     */
    private $digest;
    /**
     * @var string
     */
    private $signature;

    public function __construct(string $serial, string $digest, string $signature)
    {
        $this->serial = $serial;
        $this->digest = $digest;
        $this->signature = $signature;
    }

    /**
     * @return string
     */
    public function getSerial(): string
    {
        return $this->serial;
    }

    /**
     * @return string
     */
    public function getDigest(): string
    {
        return $this->digest;
    }

    /**
     * @return string
     */
    public function getSignature(): string
    {
        return $this->signature;
    }
}
