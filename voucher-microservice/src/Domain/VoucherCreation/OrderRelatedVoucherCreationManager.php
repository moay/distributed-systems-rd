<?php

namespace App\Domain\VoucherCreation;

use App\Domain\MessageBus\DataTransfer\OrderInformationTransfer;
use App\Domain\VoucherCreation\VoucherCreationStrategy\OrderRelatedVoucherCreationStrategyInterface;
use App\Event\VoucherCreatedEvent;
use App\Handler\VoucherHandler;
use Psr\EventDispatcher\EventDispatcherInterface;

class OrderRelatedVoucherCreationManager
{
    /** @var OrderRelatedVoucherCreationStrategyInterface[] */
    private $voucherCreators = [];

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var VoucherHandler */
    private $voucherHandler;

    /**
     * OrderRelatedVoucherCreationManager constructor.
     *
     * @param OrderRelatedVoucherCreationStrategyInterface[] $voucherCreators
     */
    public function __construct(
        iterable $voucherCreators,
        EventDispatcherInterface $eventDispatcher,
        VoucherHandler $voucherHandler
    ) {
        foreach ($voucherCreators as $creator) {
            if (!$creator instanceof OrderRelatedVoucherCreationStrategyInterface) {
                throw new \InvalidArgumentException('Please inject only order related voucher creators here');
            }
            $this->voucherCreators[] = $creator;
        }
        $this->eventDispatcher = $eventDispatcher;
        $this->voucherHandler = $voucherHandler;
    }

    public function handleOrder(OrderInformationTransfer $orderInformationTransfer)
    {
        foreach ($this->voucherCreators as $creator) {
            if ($creator->isRelevantOrder($orderInformationTransfer)) {
                $voucher = $creator->generateVoucher($orderInformationTransfer);
                $this->voucherHandler->persistVoucher($voucher);
                $this->eventDispatcher->dispatch(new VoucherCreatedEvent($voucher));
            }
        }
    }
}
