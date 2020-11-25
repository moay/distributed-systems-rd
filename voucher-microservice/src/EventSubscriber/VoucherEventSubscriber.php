<?php

namespace App\EventSubscriber;

use App\Event\VoucherCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class VoucherEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            VoucherCreatedEvent::class => 'handleVoucherCreation',
        ];
    }

    public function handleVoucherCreation(VoucherCreatedEvent $event)
    {
        // Todo: Handle voucher creation events. Maybe push this to rabbit MQ for backporting the information into the order microservice.
    }
}
