<?php

namespace App\Domain\MessageBus;

use App\Domain\MessageBus\Message\BusMessageInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class MessageBusJsonSerializer implements SerializerInterface
{
    public function decode(array $encodedEnvelope): Envelope
    {
        $data = json_decode($encodedEnvelope['body'], true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new MessageDecodingFailedException('Invalid json encountered');
        }
        $messageType = $encodedEnvelope['headers']['type'];
        if (!class_exists($messageType)) {
            throw new UnrecoverableMessageHandlingException('Unknown message type received');
        }

        /** @var $messageType BusMessageInterface */
        $message = $messageType::createFromDecodedEnvelopeContents($data);

        if (isset($headers['stamps'])) {
            $stamps = unserialize($headers['stamps']);
        }

        return new Envelope($message, $stamps ?? []);
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();
        if (!$message instanceof \JsonSerializable || !$message instanceof BusMessageInterface) {
            throw new \InvalidArgumentException('Cannot encode non message bus messages');
        }

        $allStamps = [];
        foreach ($envelope->all() as $stamps) {
            $allStamps = array_merge($allStamps, $stamps);
        }

        return [
            'body' => json_encode($message),
            'headers' => [
                'stamps' => serialize($allStamps),
            ],
        ];
    }
}
