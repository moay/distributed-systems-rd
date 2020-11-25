<?php

namespace App\Handler;

use App\DataTransfer\OrderTransfer;
use App\Domain\Tax\OrderVatRateProvider;
use App\Entity\Order;
use App\Event\OrderCreatedEvent;
use App\Event\OrderUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderHandler
{
    /** @var OrderVatRateProvider */
    private $orderVatProvider;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * OrderHandler constructor.
     */
    public function __construct(
        OrderVatRateProvider $orderVatProvider,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->orderVatProvider = $orderVatProvider;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createOrder(OrderTransfer $orderTransfer): Order
    {
        $order = new Order();
        $order->setTotal((float) $orderTransfer->netTotal);

        $vatRate = $this->orderVatProvider->getVatRateForOrder($order);
        $order->setVatRate($vatRate);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new OrderCreatedEvent($order));

        return $order;
    }

    public function markOrderAsDelivered(Order $order)
    {
        $order->setDeliverySent(true);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new OrderUpdatedEvent($order));
    }
}
