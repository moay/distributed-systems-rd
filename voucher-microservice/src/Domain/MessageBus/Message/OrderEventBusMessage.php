<?php

namespace App\Domain\MessageBus\Message;

use App\Domain\MessageBus\DataTransfer\OrderInformationTransfer;
use App\Domain\MessageBus\Payload\OrderInformationMessageBusMessagePayload;

class OrderEventBusMessage implements BusMessageInterface
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

    /**
     * @return OrderEventBusMessage
     */
    public static function createFromDecodedEnvelopeContents(array $decodedEnvelopeBody): BusMessageInterface
    {
        $data = $decodedEnvelopeBody['payload'] ?? [];

        $payload = new OrderInformationMessageBusMessagePayload();
        $payload->setIsUpdate($data['event'] ?? null === 'Order.Updated');

        $order = $data['order'];
        $orderInformationTransfer = new OrderInformationTransfer();
        $orderInformationTransfer->id = $order['id'] ?? null;
        $orderInformationTransfer->orderDate = $order['orderDate'] ?? null;
        $orderInformationTransfer->netTotal = $order['netTotal'] ?? null;
        $orderInformationTransfer->vatRate = $order['vatRate'] ?? null;
        $orderInformationTransfer->deliverySent = $order['deliverySent'] ?? null;
        $payload->setOrderInformationTransfer($orderInformationTransfer);

        return new self($payload);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'payload' => $this->payload,
        ];
    }
}
