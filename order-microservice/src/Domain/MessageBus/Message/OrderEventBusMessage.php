<?php

namespace App\Domain\MessageBus\Message;

use App\Domain\MessageBus\Payload\OrderInformationMessageBusMessagePayload;

class OrderEventBusMessage
{
    /** @var OrderInformationMessageBusMessagePayload */
    private $payload;

    /**
     * OrderEventBusMessage constructor.
     */
    public function __construct(OrderInformationMessageBusMessagePayload $payload)
    {
        $this->payload = $payload;
    }

    public function getPayload(): OrderInformationMessageBusMessagePayload
    {
        return $this->payload;
    }
}
