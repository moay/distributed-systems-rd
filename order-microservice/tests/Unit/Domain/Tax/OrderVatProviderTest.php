<?php

namespace App\Tests\Unit\Domain\Tax;

use App\Domain\Tax\OrderVatRateProvider;
use App\Entity\Order;
use PHPUnit\Framework\TestCase;

class OrderVatProviderTest extends TestCase
{
    public function testRegularVatFor2021Is19Percent()
    {
        $order = new Order();
        $order->setCreatedAt(new \DateTimeImmutable('2021-01-02 00:00:00'));

        $vatRateProvider = new OrderVatRateProvider();
        $this->assertEquals(0.19, $vatRateProvider->getVatRateForOrder($order));
    }

    public function testVatRateForCoronaIs16Percent()
    {
        $order = new Order();
        $order->setCreatedAt(new \DateTimeImmutable('2020-07-02 00:00:00'));

        $vatRateProvider = new OrderVatRateProvider();
        $this->assertEquals(0.16, $vatRateProvider->getVatRateForOrder($order));
    }
}
