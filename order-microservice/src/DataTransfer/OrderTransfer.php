<?php

namespace App\DataTransfer;

class OrderTransfer
{
    /** @var int */
    public $netTotal;

    /**
     * Instantiates a new OrderTransfer for a given net total.
     *
     * @return static
     */
    public static function createForAmount(int $netTotal): self
    {
        $orderTransfer = new self();
        $orderTransfer->netTotal = $netTotal;

        return $orderTransfer;
    }
}
