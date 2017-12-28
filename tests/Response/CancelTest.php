<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Response;

class CancelTest extends TestCase
{
    public function testGetAmount()
    {
        $response = new Cancel($this->createPsr7Response(['NewAmount' => 1000]));
        $this->assertEquals(1000, $response->getAmount());
    }

    public function testGetPreviousAmount()
    {
        $response = new Cancel($this->createPsr7Response(['OriginalAmount' => 2000]));
        $this->assertEquals(2000, $response->getPreviousAmount());
    }

    public function testGetStatus()
    {
        $response = new Cancel($this->createPsr7Response(['Status' => 'foo']));
        $this->assertEquals('foo', $response->getStatus());
    }

    public function testGetOrderId()
    {
        $response = new Cancel($this->createPsr7Response(['OrderId' => 'SomeOrderId']));
        $this->assertEquals('SomeOrderId', $response->getOrderId());
    }

    public function testGetPaymentId()
    {
        $response = new Cancel($this->createPsr7Response(['PaymentId' => 'SomePaymentId']));
        $this->assertEquals('SomePaymentId', $response->getPaymentId());
    }
}
