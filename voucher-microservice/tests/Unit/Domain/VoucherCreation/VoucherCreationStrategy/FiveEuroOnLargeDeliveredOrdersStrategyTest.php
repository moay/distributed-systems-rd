<?php

namespace App\Tests\Unit\Domain\VoucherCreation\VoucherCreationStrategy;

use App\Domain\MessageBus\DataTransfer\OrderInformationTransfer;
use App\Domain\VoucherCreation\VoucherCreationStrategy\FiveEuroOnLargeDeliveredOrdersStrategy;
use App\Entity\Voucher;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class FiveEuroOnLargeDeliveredOrdersStrategyTest extends TestCase
{
    /** @dataProvider largeOrdersProvider */
    public function testLargeDeliveredOrdersAreRelevant($orderValue, $orderVatRate)
    {
        $entityManager = $this->createMock(EntityManager::class);
        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->expects($this->any())->method('findOneBy')->willReturn(null);
        $entityManager->expects($this->any())->method('getRepository')->willReturn($repositoryMock);

        $strategy = new FiveEuroOnLargeDeliveredOrdersStrategy($entityManager);
        $orderInformation = $this->generateOrderInformation($orderValue, $orderVatRate, true);

        $this->assertTrue($strategy->isRelevantOrder($orderInformation));
    }

    /** @dataProvider largeOrdersProvider */
    public function testRelevantOrdersWillNotBeRelevantTwice($orderValue, $orderVatRate)
    {
        $voucher = new Voucher();

        $entityManager = $this->createMock(EntityManager::class);
        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->expects($this->any())->method('findOneBy')->willReturn($voucher);
        $entityManager->expects($this->any())->method('getRepository')->willReturn($repositoryMock);

        $strategy = new FiveEuroOnLargeDeliveredOrdersStrategy($entityManager);
        $orderInformation = $this->generateOrderInformation($orderValue, $orderVatRate, true);

        $this->assertFalse($strategy->isRelevantOrder($orderInformation));
    }

    /** @dataProvider largeOrdersProvider */
    public function testLargeUndeliveredOrdersAreIrrelevant($orderValue, $orderVatRate)
    {
        $entityManager = $this->createMock(EntityManager::class);
        $strategy = new FiveEuroOnLargeDeliveredOrdersStrategy($entityManager);
        $orderInformation = $this->generateOrderInformation($orderValue, $orderVatRate, false);

        $this->assertFalse($strategy->isRelevantOrder($orderInformation));
    }

    /** @dataProvider smallOrdersProvider */
    public function testSmallDeliveredOrdersAreIrrelevant($orderValue, $orderVatRate)
    {
        $entityManager = $this->createMock(EntityManager::class);
        $strategy = new FiveEuroOnLargeDeliveredOrdersStrategy($entityManager);
        $orderInformation = $this->generateOrderInformation($orderValue, $orderVatRate, false);

        $this->assertFalse($strategy->isRelevantOrder($orderInformation));
    }

    /** @dataProvider largeOrdersProvider */
    public function testGeneratedVoucherHasCorrectValueAndType($orderValue, $orderVatRate)
    {
        $entityManager = $this->createMock(EntityManager::class);
        $strategy = new FiveEuroOnLargeDeliveredOrdersStrategy($entityManager);
        $orderInformation = $this->generateOrderInformation($orderValue, $orderVatRate, true);

        $voucher = $strategy->generateVoucher($orderInformation);

        $this->assertInstanceOf(Voucher::class, $voucher);
        $this->assertEquals(5, $voucher->getValue());
        $this->assertFalse($voucher->isRedeemed());
        $this->assertEquals(Voucher::TYPE_ABSOLUTE, $voucher->getType());
    }

    public function largeOrdersProvider()
    {
        return [
            [100, 0],
            [87, 19],
            [2500, 16],
        ];
    }

    public function smallOrdersProvider()
    {
        return [
            [99, 0],
            [9, 1100],
            [86, 16],
        ];
    }

    private function generateOrderInformation($orderValue, $orderVatRate, $deliverySent): OrderInformationTransfer
    {
        $orderInformation = new OrderInformationTransfer();
        $orderInformation->netTotal = $orderValue;
        $orderInformation->vatRate = $orderVatRate;
        $orderInformation->deliverySent = $deliverySent;
        $orderInformation->id = 'testid';

        return $orderInformation;
    }
}
