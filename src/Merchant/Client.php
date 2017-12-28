<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Merchant;

use GuzzleHttp\Psr7\Request;
use Personnage\Tinkoff\SDK\Exception\HttpException;
use Personnage\Tinkoff\SDK\HasSender;
use function Personnage\Tinkoff\SDK\pack;
use Personnage\Tinkoff\SDK\Exception\ResponseException;
use Personnage\Tinkoff\SDK\Response\Cancel;
use Personnage\Tinkoff\SDK\Response\Charge;
use Personnage\Tinkoff\SDK\Response\Confirm;
use Personnage\Tinkoff\SDK\Response\Init;
use Personnage\Tinkoff\SDK\Sender;
use Psr\Http\Message\RequestInterface;

final class Client
{
    use HasSignature, HasSender;

    private $baseUri;
    private $terminalKey;
    private $secretKey;

    /**
     * @var array
     */
    private static $requestHeaders = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/x-www-form-urlencoded',
    ];

    /**
     * Init a new instance.
     *
     * @param string  $uri
     * @param string  $terminalKey
     * @param string  $secretKey
     * @param Sender  $sender
     */
    public function __construct(string $uri, string $terminalKey, string $secretKey, Sender $sender)
    {
        $this->baseUri = rtrim($uri, '/');
        $this->terminalKey = $terminalKey;
        $this->secretKey = $secretKey;
        $this->setSender($sender);
    }

    /**
     * Init a new payment session.
     *
     * @param string  $orderId Номер заказа в системе Продавца.
     * @param int     $amount  Сумма в копейках.
     * @param array   $extra   Массив дополнительных полей, которые могут быть переданы в этом запросе.
     *
     * @return Init
     * @throws HttpException
     */
    public function init(string $orderId, int $amount, array $extra = []): Init
    {
        $extra['Amount'] = $amount;
        $extra['OrderId'] = $orderId;

        return new Init($this->send($this->makeRequest('Init', $extra)));
    }

    /**
     * Init a new payment session.
     *
     * @param string  $customerKey
     * @param string  $orderId
     * @param int     $amount
     * @param array   $extra
     *
     * @return Init
     * @throws HttpException
     */
    public function rInit(string $customerKey, string $orderId, int $amount, array $extra = []): Init
    {
        $extra['Recurrent'] = 'Y';
        $extra['CustomerKey'] = $customerKey;

        return $this->init($orderId, $amount, $extra);
    }

    /**
     * Call charge.
     *
     * @param mixed  $paymentId
     * @param mixed  $rebillId
     *
     * @return Charge
     *
     * @throws HttpException
     */
    public function charge($paymentId, $rebillId): Charge
    {
        return new Charge($this->send($this->makeRequest('Charge', [
            'PaymentId' => $paymentId,
            'RebillId' => $rebillId,
        ])));
    }

    /**
     * Init a new payment session and call charge.
     *
     * @param mixed   $rebillId
     * @param string  $orderId
     * @param int     $amount
     * @param array   $extra
     *
     * @return Charge
     * @throws HttpException
     * @throws ResponseException
     */
    public function recurrent($rebillId, string $orderId, int $amount, array $extra = []): Charge
    {
        $response = $this->init($orderId, $amount, $extra);
        if ($response->hasError()) {
            throw new ResponseException($response);
        }

        $response = $this->charge($response->getPaymentId(), $rebillId);
        if ($response->hasError()) {
            throw new ResponseException($response);
        }

        return $response;
    }

    /**
     * Send confirm request.
     *
     * @param int       $paymentId
     * @param int|null  $amount
     * @param array     $extra
     *
     * @return Confirm
     * @throws HttpException
     */
    public function confirm(int $paymentId, int $amount = null, array $extra = []): Confirm
    {
        $extra['PaymentId'] = $paymentId;

        if (null !== $amount) {
            $extra['Amount'] = $amount;
        }

        return new Confirm($this->send($this->makeRequest('Confirm', $extra)));
    }

    /**
     * Send cancel request.
     *
     * @param int       $paymentId
     * @param int|null  $amount
     * @param array     $extra
     *
     * @return Cancel
     * @throws HttpException
     */
    public function cancel(int $paymentId, int $amount = null, array $extra = []): Cancel
    {
        $extra['PaymentId'] = $paymentId;

        if (null !== $amount) {
            $extra['Amount'] = $amount;
        }

        return new Cancel($this->send($this->makeRequest('Cancel', $extra)));
    }

    /**
     * Make a new http request.
     *
     * @param string  $uri
     * @param array   $body
     *
     * @return RequestInterface
     */
    private function makeRequest(string $uri, array $body = []): RequestInterface
    {
        if (isset($body['DATA'])) {
            $body['DATA'] = pack($body['DATA']);
        }

        $body['TerminalKey'] = $this->terminalKey;
        $body['Token'] = $this->sign($body, $this->secretKey);

        return new Request('post', "$this->baseUri/$uri", self::$requestHeaders, http_build_query($body, '', '&'));
    }
}
