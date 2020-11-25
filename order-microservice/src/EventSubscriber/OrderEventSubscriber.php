<?php

namespace App\EventSubscriber;

use App\Event\OrderCreatedEvent;
use App\Event\OrderUpdatedEvent;
use App\Message\OrderEventMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderEventSubscriber implements EventSubscriberInterface
{
    /** @var MessageBusInterface */
    private $messageBus;

    /**
     * OrderEventSubscriber constructor.
     */
    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            OrderCreatedEvent::class => 'triggerOrderEventMessage',
            OrderUpdatedEvent::class => 'triggerOrderEventMessage',
        ];
    }

    /**
     * @param OrderCreatedEvent|OrderUpdatedEvent|object $event
     */
    public function triggerOrderEventMessage(object $event)
    {
        $this->messageBus->dispatch(new OrderEventMessage($event->getOrder()->getId()));
    }
}
