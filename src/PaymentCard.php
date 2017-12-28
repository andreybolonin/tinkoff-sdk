<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK;

final class PaymentCard
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $number;
    /**
     * @var \DateTimeInterface
     */
    private $expired;

    /**
     * Create a new instance.
     *
     * @param string             $id
     * @param string             $number
     * @param \DateTimeInterface $expired
     */
    public function __construct(string $id, string $number, \DateTimeInterface $expired)
    {
        $this->id = $id;
        $this->number = $number;
        $this->expired = $expired;
    }

    /**
     * Create a new instance.
     *
     * @param  mixed   $id
     * @param  string  $number
     * @param  string  $expired
     * @param  string  $dateFormat
     *
     * @return PaymentCard
     */
    public static function make($id, string $number, string $expired, string $dateFormat = 'my'): self
    {
        return new self((string) $id, $number, \DateTimeImmutable::createFromFormat($dateFormat, $expired));
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getExpired(): \DateTimeInterface
    {
        return $this->expired;
    }
}
