<?php

namespace App\Domain\VoucherCreation\VoucherCreationStrategy;

use App\Domain\MessageBus\DataTransfer\OrderInformationTransfer;
use App\Entity\Voucher;

interface OrderRelatedVoucherCreationStrategyInterface extends VoucherCreationStrategyInterface
{
    public function isRelevantOrder(OrderInformationTransfer $orderInformationTransfer): bool;

    public function generateVoucher(OrderInformationTransfer $orderInformationTransfer): Voucher;
}
