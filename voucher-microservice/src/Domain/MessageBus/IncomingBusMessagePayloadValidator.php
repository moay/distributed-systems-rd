<?php

namespace App\Domain\MessageBus;

use App\Domain\MessageBus\DataTransfer\OrderInformationTransfer;
use App\Domain\MessageBus\Payload\OrderInformationMessageBusMessagePayload;

class IncomingBusMessagePayloadValidator
{
    /**
     * Very basic validation of message contents. This should probably be more sophisticated in a real life scenario.
     */
    public function validatePayload(OrderInformationMessageBusMessagePayload $payload): bool
    {
        $orderInformationTransfer = $payload->getOrderInformationTransfer();
        if (!$orderInformationTransfer instanceof OrderInformationTransfer) {
            return false;
        }

        foreach (['id', 'netTotal', 'vatRate', 'orderDate', 'deliverySent'] as $attributeName) {
            if (null === $orderInformationTransfer->{$attributeName}) {
                return false;
            }
        }

        return true;
    }
}
