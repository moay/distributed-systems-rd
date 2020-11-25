<?php

namespace App\Tests\Integration;

use App\Domain\MessageBus\DataTransfer\OrderInformationTransfer;
use App\Domain\MessageBus\Message\OrderEventBusMessage;
use App\Domain\MessageBus\MessageBusJsonSerializer;
use App\Domain\MessageBus\Payload\OrderInformationMessageBusMessagePayload;
use App\Entity\Voucher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderEventMessageBusIntegrationTest extends IntegrationTestCase
{
    public function testValidOrderMessageLeadsToANewVoucher()
    {
        self::bootKernel();
        $messageBus = self::$container->get(MessageBusInterface::class);
        $entityManager = self::$container->get(EntityManagerInterface::class);

        $orderInformationTransfer = new OrderInformationTransfer();
        $orderInformationTransfer->deliverySent = true;
        $orderInformationTransfer->id = 'voucherCreatingOrder';
        $orderInformationTransfer->vatRate = 0.19;
        $orderInformationTransfer->netTotal = 100;
        $orderInformationTransfer->orderDate = new \DateTimeImmutable();

        $payload = new OrderInformationMessageBusMessagePayload();
        $payload->setIsUpdate(true);
        $payload->setOrderInformationTransfer($orderInformationTransfer);

        $messageBus->dispatch(new OrderEventBusMessage($payload));

        $voucher = $entityManager->getRepository(Voucher::class)->findOneBy(['relatedOrderId' => 'voucherCreatingOrder']);
        $this->assertInstanceOf(Voucher::class, $voucher);
        $this->assertEquals('voucherCreatingOrder', $voucher->getRelatedOrderId());
    }

    public function testValidButNotSentOrderMessageLeadsNotToANewVoucher()
    {
        self::bootKernel();
        $messageBus = self::$container->get(MessageBusInterface::class);
        $entityManager = self::$container->get(EntityManagerInterface::class);

        $orderInformationTransfer = new OrderInformationTransfer();
        $orderInformationTransfer->deliverySent = false;
        $orderInformationTransfer->id = 'voucherCreatingOrderButNotSent';
        $orderInformationTransfer->vatRate = 0.19;
        $orderInformationTransfer->netTotal = 100;
        $orderInformationTransfer->orderDate = new \DateTimeImmutable();

        $payload = new OrderInformationMessageBusMessagePayload();
        $payload->setIsUpdate(true);
        $payload->setOrderInformationTransfer($orderInformationTransfer);

        $messageBus->dispatch(new OrderEventBusMessage($payload));

        $voucher = $entityManager->getRepository(Voucher::class)->findOneBy(['relatedOrderId' => 'voucherCreatingOrderButNotSent']);
        $this->assertNull($voucher);
    }

    public function testInvalidJsonLeadsToException()
    {
        self::bootKernel();
        $messageBus = self::$container->get(MessageBusInterface::class);
        $serializer = self::$container->get(MessageBusJsonSerializer::class);

        $encodedMessage = [
            'body' => '{"payload":{"event":"Order.Updated","order":{"id":"voucherCreatingOrderButNotSent""netTotal":100,"vatRate":0.19,"deliverySent":false,"orderDate":{"date":"2020-11-25 08:34:26.537823","timezone_type":3,"timezone":"UTC"}}}}',
            'headers' => [
                'stamps' => 'a:0:{}',
                'type' => OrderEventBusMessage::class,
            ],
        ];

        $this->expectException(MessageDecodingFailedException::class);
        $message = $serializer->decode($encodedMessage);
    }

    public function testInvalidOrderMessageLeadsToException()
    {
        self::bootKernel();
        $messageBus = self::$container->get(MessageBusInterface::class);

        $orderInformationTransfer = new OrderInformationTransfer();
        $orderInformationTransfer->deliverySent = true;
        $orderInformationTransfer->vatRate = 0.19;
        $orderInformationTransfer->netTotal = 100;
        $orderInformationTransfer->orderDate = new \DateTimeImmutable();
        // ID is missing here, this should fail

        $payload = new OrderInformationMessageBusMessagePayload();
        $payload->setIsUpdate(true);
        $payload->setOrderInformationTransfer($orderInformationTransfer);

        $this->expectException(HandlerFailedException::class);
        $this->expectExceptionMessage('Invalid message payload received, could not handle message.');
        $messageBus->dispatch(new OrderEventBusMessage($payload));
    }
}
