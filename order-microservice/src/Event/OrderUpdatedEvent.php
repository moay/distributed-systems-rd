<?php

namespace App\Event;

use App\Entity\Order;
use Symfony\Contracts\EventDispatcher\Event;

class OrderUpdatedEvent extends Event
{
    /** @var Order */
    private $order;

    /**
     * OrderCreatedEvent constructor.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
