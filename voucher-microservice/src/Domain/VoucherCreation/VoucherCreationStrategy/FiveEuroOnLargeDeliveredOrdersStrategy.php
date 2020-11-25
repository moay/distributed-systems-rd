<?php

namespace App\Domain\VoucherCreation\VoucherCreationStrategy;

use App\Domain\MessageBus\DataTransfer\OrderInformationTransfer;
use App\Domain\VoucherCreation\VoucherFactory;
use App\Entity\Voucher;
use Doctrine\ORM\EntityManagerInterface;

class FiveEuroOnLargeDeliveredOrdersStrategy implements OrderRelatedVoucherCreationStrategyInterface
{
    const STRATEGY_NAME = 'Order.Large-Delivered-Five-Euro';

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * FiveEuroOnLargeDeliveredOrdersStrategy constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getStrategyName(): string
    {
        return self::STRATEGY_NAME;
    }

    /**
     * Checks if a voucher should be generated.
     */
    public function isRelevantOrder(OrderInformationTransfer $orderInformationTransfer): bool
    {
        $orderTotal = $orderInformationTransfer->netTotal * (1 + $orderInformationTransfer->vatRate);
        if ($orderTotal < 100 || !$orderInformationTransfer->deliverySent) {
            return false;
        }

        $alreadyCreatedVoucher = $this->entityManager->getRepository(Voucher::class)->findOneBy([
            'relatedOrderId' => $orderInformationTransfer->id,
            'creationStrategy' => $this->getStrategyName(),
        ]);

        return !$alreadyCreatedVoucher instanceof Voucher;
    }

    /**
     * Generates the needed Voucher.
     */
    public function generateVoucher(OrderInformationTransfer $orderInformationTransfer): Voucher
    {
        return VoucherFactory::createVoucher(
            Voucher::TYPE_ABSOLUTE,
            $this->getStrategyName(),
            5,
            $orderInformationTransfer->id
        );
    }
}
