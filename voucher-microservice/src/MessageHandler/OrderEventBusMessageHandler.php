<?php

namespace App\MessageHandler;

use App\Domain\MessageBus\IncomingBusMessagePayloadValidator;
use App\Domain\MessageBus\Message\OrderEventBusMessage;
use App\Message\OrderInformationMessage;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderEventBusMessageHandler implements MessageHandlerInterface
{
    /** @var MessageBusInterface */
    private $messageBus;

    /** @var IncomingBusMessagePayloadValidator */
    private $payloadValidator;

    /**
     * OrderEventBusMessageHandler constructor.
     */
    public function __construct(IncomingBusMessagePayloadValidator $payloadValidator, MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
        $this->payloadValidator = $payloadValidator;
    }

    /**
     * Validates the payload and moves the message to a local queue.
     */
    public function __invoke(OrderEventBusMessage $message)
    {
        if (!$this->payloadValidator->validatePayload($message->getPayload())) {
            throw new UnrecoverableMessageHandlingException('Invalid message payload received, could not handle message.');
        }

        $this->messageBus->dispatch(new OrderInformationMessage($message->getPayload()->getOrderInformationTransfer()));
    }
}
