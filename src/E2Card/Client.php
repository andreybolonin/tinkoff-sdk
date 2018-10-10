<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\E2Card;

use Closure;
use GuzzleHttp\Psr7\Request;
use Personnage\Tinkoff\SDK\Exception\HttpException;
use Personnage\Tinkoff\SDK\Exception\ResponseException;
use Personnage\Tinkoff\SDK\HasSender;
use Personnage\Tinkoff\SDK\Response\Init;
use Personnage\Tinkoff\SDK\Response\Payment;
use Personnage\Tinkoff\SDK\Response\State;
use Personnage\Tinkoff\SDK\Sender;
use Psr\Http\Message\RequestInterface;

final class Client
{
    use HasSignature, HasSender;

    private $baseUri;
    private $terminalKey;
    private $certNumber;
    private $signer;

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
     * @param string  $certNumber
     * @param Closure $signer
     * @param Sender  $sender
     */
    public function __construct(string $uri, string $terminalKey, string $certNumber, Closure $signer, Sender $sender)
    {
        $this->baseUri = rtrim($uri, '/');
        $this->terminalKey = $terminalKey;
        $this->certNumber = $certNumber;
        $this->signer = $signer;
        $this->setSender($sender);
    }

    /**
     * Create a new instance.
     *
     * @param string   $uri
     * @param string   $terminalKey
     * @param string   $pemFile
     * @param Sender   $sender
     * @return self
     */
    public static function make(string $uri, string $terminalKey, string $pemFile, Sender $sender): self
    {
        $signer = function ($message) use ($pemFile): string {
            return self::sign($message, realpath($pemFile));
        };

        return new self($uri, $terminalKey, self::getSerialNumber($pemFile), $signer, $sender);
    }

    /**
     * Get serial number of X509.
     *
     * @param  string  $pemFile
     * @return string
     */
    private static function getSerialNumber(string $pemFile): string
    {
        $text = openssl_x509_parse('file://'.$pemFile, true);

        return $text['serialNumber'];
    }

    /**
     * Init a new payment session.
     *
     * @param string $orderId Номер заказа в системе Продавца.
     * @param string $cardId  Идентификатор карты пополнения.
     * @param int    $amount  Сумма в копейках.
     * @param array  $extra   Массив дополнительных полей, которые могут быть переданы в этом запросе.
     *
     * @return Init
     * @throws HttpException
     */
    public function init(string $orderId, string $cardId, int $amount, array $extra = []): Init
    {
        $extra['Amount'] = $amount;
        $extra['CardId'] = $cardId;
        $extra['OrderId'] = $orderId;

        return new Init($this->send($this->makeRequest('Init', $extra)));
    }

    /**
     * Make payout to card.
     *
     * @param mixed $paymentId
     *
     * @return Payment
     * @throws HttpException
     */
    public function payment($paymentId): Payment
    {
        return new Payment($this->send($this->makeRequest('Payment', ['PaymentId' => $paymentId])));
    }

    /**
     * Get op state.
     *
     * @param mixed $paymentId
     *
     * @return State
     * @throws HttpException
     */
    public function getState($paymentId): State
    {
        return new State($this->send($this->makeRequest('GetState', ['PaymentId' => $paymentId])));
    }

    /**
     * Init a new payment session and make payout to card.
     *
     * @param string $orderId Номер заказа в системе Продавца.
     * @param string $cardId  Идентификатор карты пополнения.
     * @param int    $amount  Сумма в копейках.
     * @param array  $extra   Массив дополнительных полей, которые могут быть переданы в этом запросе.
     *
     * @return Payment
     * @throws HttpException
     * @throws ResponseException
     */
    public function payout(string $orderId, string $cardId, int $amount, array $extra = []): Payment
    {
        $response = $this->init($orderId, $cardId, $amount, $extra);
        if ($response->hasError() || 'CHECKED' !== $response->getStatus()) {
            throw new ResponseException($response);
        }

        $response = $this->payment($response->getPaymentId());
        if ($response->hasError() || 'COMPLETED' !== $response->getStatus()) {
            throw new ResponseException($response);
        }

        return $response;
    }

    /**
     * Make a new http request.
     *
     * @param string $uri
     * @param array  $body
     *
     * @return RequestInterface
     */
    private function makeRequest(string $uri, array $body = []): RequestInterface
    {
        $body['TerminalKey'] = $this->terminalKey;
        $body['DigestValue'] = base64_encode(self::digest($body));
        $body['SignatureValue'] = base64_encode(\call_user_func($this->signer, $body));
        $body['X509SerialNumber'] = $this->certNumber;

        return new Request('post', "$this->baseUri/$uri", self::$requestHeaders, http_build_query($body, '', '&'));
    }
}
