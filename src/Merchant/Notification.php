<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Merchant;

use function Personnage\Tinkoff\SDK\message_get_body;
use Personnage\Tinkoff\SDK\Exception\InvalidToken;
use Personnage\Tinkoff\SDK\PaymentCard;
use Psr\Http\Message\RequestInterface;

final class Notification
{
    use HasSignature;

    const AUTHORIZED = 'AUTHORIZED';
    const CONFIRMED = 'CONFIRMED';
    const PARTIAL_REFUNDED = 'PARTIAL_REFUNDED';
    const REFUNDED = 'REFUNDED';
    const REJECTED = 'REJECTED';
    const REVERSED = 'REVERSED';

    /**
     * @var array
     */
    private $values;

    /**
     * @var PaymentCard
     */
    private $paymentCard;

    /**
     * Create a new instance.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['DATA'])) {
            $values['DATA'] = urldecode($values['DATA']);
        }

        $this->values = $values;
    }

    /**
     * Create a new instance from request instance.
     *
     * @param  RequestInterface  $request
     * @return self
     */
    public static function fromRequest(RequestInterface $request): self
    {
        return new self(message_get_body($request));
    }

    /**
     * Throws an exception if token is invalid.
     *
     * @param  string  $secret
     * @throws InvalidToken
     */
    public function validate(string $secret)
    {
        if ($this->sign($this->values, $secret) !== $this->get('Token')) {
            throw new InvalidToken('Invalid token.');
        }
    }

    /**
     * Is request successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return 'true' === $this->get('Success');
    }

    /**
     * Is request authorized?
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return self::AUTHORIZED === $this->get('Status');
    }

    /**
     * Is request confirmed?
     *
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return self::CONFIRMED === $this->get('Status');
    }

    /**
     * Is request reversed?
     *
     * @return bool
     */
    public function isReversed(): bool
    {
        return self::REVERSED === $this->get('Status');
    }

    /**
     * Is request refunded?
     *
     * @return bool
     */
    public function isRefunded(): bool
    {
        return self::REFUNDED === $this->get('Status');
    }

    /**
     * Is request refunded?
     *
     * @return bool
     */
    public function isPartialRefunded(): bool
    {
        return self::PARTIAL_REFUNDED === $this->get('Status');
    }

    /**
     * Is request rejected?
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return self::REJECTED === $this->get('Status');
    }

    /**
     * Get a payment card instance if card id exists.
     *
     * @return PaymentCard|null
     */
    public function getPaymentCard()
    {
        if ($this->paymentCard || $this->get('CardId')) {
            $this->paymentCard = PaymentCard::make($this->get('CardId'), $this->get('Pan'), $this->get('ExpDate'));
        }

        return $this->paymentCard;
    }

    /**
     * Get same value by key.
     *
     * @param  string  $key
     * @return string|null
     */
    public function get(string $key)
    {
        return $this->values[$key] ?? null;
    }

    /**
     * Get all values as JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Get all values as a plain array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->values;
    }
}
