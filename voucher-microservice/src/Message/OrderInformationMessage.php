<?php

namespace App\Message;

use App\Domain\MessageBus\DataTransfer\OrderInformationTransfer;

class OrderInformationMessage
{
    /** @var OrderInformationTransfer */
    private $orderInformationTransfer;

    /**
     * OrderEventMessage constructor.
     */
    public function __construct(OrderInformationTransfer $orderInformationTransfer)
    {
        $this->orderInformationTransfer = $orderInformationTransfer;
    }

    public function getOrderInformationTransfer(): OrderInformationTransfer
    {
        return $this->orderInformationTransfer;
    }
}
