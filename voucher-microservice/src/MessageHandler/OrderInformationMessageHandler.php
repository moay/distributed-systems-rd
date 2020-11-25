<?php

namespace App\MessageHandler;

use App\Domain\VoucherCreation\OrderRelatedVoucherCreationManager;
use App\Message\OrderInformationMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class OrderInformationMessageHandler implements MessageHandlerInterface
{
    /** @var OrderRelatedVoucherCreationManager */
    private $voucherCreationManager;

    /**
     * OrderInformationMessageHandler constructor.
     */
    public function __construct(OrderRelatedVoucherCreationManager $voucherCreationManager)
    {
        $this->voucherCreationManager = $voucherCreationManager;
    }

    public function __invoke(OrderInformationMessage $message)
    {
        $this->voucherCreationManager->handleOrder($message->getOrderInformationTransfer());
    }
}
