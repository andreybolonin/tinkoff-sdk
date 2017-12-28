<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Response;

use function Personnage\Tinkoff\SDK\message_get_body;
use Psr\Http\Message\ResponseInterface;

final class Init extends Response
{
    use HasPsr7Response;

    private $body;

    public function __construct(ResponseInterface $response)
    {
        $this->body = message_get_body($response);
        $this->setPsr7Response($response);
    }

    public function getAmount()
    {
        return $this->body['Amount'] ?? null;
    }

    public function getStatus()
    {
        return $this->body['Status'] ?? null;
    }

    public function getOrderId()
    {
        return $this->body['OrderId'] ?? null;
    }

    public function getPaymentId()
    {
        return $this->body['PaymentId'] ?? null;
    }

    public function getPaymentUrl()
    {
        return $this->body['PaymentURL'] ?? null;
    }
}
