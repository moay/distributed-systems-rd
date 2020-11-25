<?php

namespace App\Domain\MessageBus\DataTransfer;

class OrderInformationTransfer implements \JsonSerializable
{
    /** @var string */
    public $id;

    /** @var float */
    public $netTotal;

    /** @var float */
    public $vatRate;

    /** @var \DateTimeImmutable */
    public $orderDate;

    /** @var bool */
    public $deliverySent;

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'netTotal' => $this->netTotal,
            'vatRate' => $this->vatRate,
            'deliverySent' => $this->deliverySent,
            'orderDate' => $this->orderDate,
        ];
    }
}
