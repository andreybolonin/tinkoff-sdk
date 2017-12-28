<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Response;

class InitTest extends TestCase
{
    public function testGetAmount()
    {
        $response = new Init($this->createPsr7Response(['Amount' => 1000]));
        $this->assertEquals(1000, $response->getAmount());
    }

    public function testGetStatus()
    {
        $response = new Init($this->createPsr7Response(['Status' => 'foo']));
        $this->assertEquals('foo', $response->getStatus());
    }

    public function testGetOrderId()
    {
        $response = new Init($this->createPsr7Response(['OrderId' => 'SomeOrderId']));
        $this->assertEquals('SomeOrderId', $response->getOrderId());
    }

    public function testGetPaymentId()
    {
        $response = new Init($this->createPsr7Response(['PaymentId' => 'SomePaymentId']));
        $this->assertEquals('SomePaymentId', $response->getPaymentId());
    }

    public function testGetPaymentUrl()
    {
        $response = new Init($this->createPsr7Response(['PaymentURL' => 'https://example.com']));
        $this->assertEquals('https://example.com', $response->getPaymentUrl());
    }
}
