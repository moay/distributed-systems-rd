<?php

namespace App\Domain\MessageBus\Payload;

use App\Domain\MessageBus\DataTransfer\OrderInformationTransfer;

class OrderInformationMessageBusMessagePayload implements \JsonSerializable
{
    public const EVENT_TYPE_CREATED = 'Order.Created';
    public const EVENT_TYPE_UPDATED = 'Order.Updated';

    /** @var OrderInformationTransfer */
    private $orderInformationTransfer;

    /** @var bool */
    private $isUpdate;

    public static function createFromDecodedPayload(array $data): OrderInformationMessageBusMessagePayload
    {
        $payload = new self();
        $payload->setIsUpdate('Order.Updated' === $data);

        $orderInformationTransfer = new OrderInformationTransfer();
        $orderInformationTransfer->id = $data['id'];
        $orderInformationTransfer->orderDate = $data['orderDate'];
        $orderInformationTransfer->netTotal = $data['netTotal'];
        $orderInformationTransfer->vatRate = $data['varRate'];
        $orderInformationTransfer->deliverySent = $data['deliverySent'];
        $payload->setOrderInformationTransfer($orderInformationTransfer);

        return $payload;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'event' => !$this->isUpdate ? self::EVENT_TYPE_CREATED : self::EVENT_TYPE_UPDATED,
            'order' => $this->orderInformationTransfer,
        ];
    }

    public function getOrderInformationTransfer(): OrderInformationTransfer
    {
        return $this->orderInformationTransfer;
    }

    public function setOrderInformationTransfer(OrderInformationTransfer $orderInformationTransfer): void
    {
        $this->orderInformationTransfer = $orderInformationTransfer;
    }

    public function isUpdate(): bool
    {
        return $this->isUpdate;
    }

    public function setIsUpdate(bool $isUpdate): void
    {
        $this->isUpdate = $isUpdate;
    }
}
