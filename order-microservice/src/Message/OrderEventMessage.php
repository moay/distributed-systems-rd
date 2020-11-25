<?php

namespace App\Message;

class OrderEventMessage
{
    /** @var int */
    private $orderId;

    /**
     * OrderEventMessage constructor.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }
}
