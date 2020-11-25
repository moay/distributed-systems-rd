<?php

namespace App\MessageHandler;

use App\Domain\MessageBus\DataTransfer\OrderInformationTransfer;
use App\Domain\MessageBus\Message\OrderEventBusMessage;
use App\Domain\MessageBus\Payload\OrderInformationMessageBusMessagePayload;
use App\Entity\Order;
use App\Message\OrderEventMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderEventMessageHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var MessageBusInterface */
    private $messageBus;

    /**
     * OrderEventBusMessageHandler constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    public function __invoke(OrderEventMessage $message)
    {
        $order = $this->entityManager->getRepository(Order::class)->find($message->getOrderId());
        if (!$order instanceof Order) {
            throw new \InvalidArgumentException(sprintf('Cannot find order with id %s, order event propagation to message bus failed.', $message->getOrderId()));
        }

        $orderInformationTransfer = $this->prepareOrderInformationTransfer($order);

        $busMessagePayload = new OrderInformationMessageBusMessagePayload();
        $busMessagePayload->setOrderInformationTransfer($orderInformationTransfer);
        $busMessagePayload->setIsUpdate(null !== $order->getUpdatedAt());

        $this->messageBus->dispatch(new OrderEventBusMessage($busMessagePayload));
    }

    private function prepareOrderInformationTransfer(Order $order): OrderInformationTransfer
    {
        $transfer = new OrderInformationTransfer();
        $transfer->id = (string) $order->getUlid();
        $transfer->orderDate = $order->getCreatedAt()->format('Y-m-d H:i:s');
        $transfer->netTotal = $order->getTotal();
        $transfer->vatRate = $order->getVatRate();
        $transfer->deliverySent = $order->isDeliverySent();

        return $transfer;
    }
}
