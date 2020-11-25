<?php

namespace App\Domain\MessageBus\Message;

interface BusMessageInterface extends \JsonSerializable
{
    public static function createFromDecodedEnvelopeContents(array $decodedEnvelopeBody): BusMessageInterface;
}
