<?php

namespace App\Domain\Tax;

use App\Entity\Order;

class OrderVatRateProvider
{
    const VAT_RATE_DEFAULT = 0.19;
    const VAT_RATE_CORONA = 0.16;

    /**
     * Provides vat rates for orders.
     *
     * Obviously, a real world vat rate resolver would be more sophisticated. The process is kept simple here in order to
     * focus on the purpose of the coding challenge.
     */
    public function getVatRateForOrder(Order $order): float
    {
        $orderDate = $order->getCreatedAt();

        if (2020 == $orderDate->format('Y') && $orderDate->format('m') > 6) {
            return self::VAT_RATE_CORONA;
        }

        return self::VAT_RATE_DEFAULT;
    }
}
