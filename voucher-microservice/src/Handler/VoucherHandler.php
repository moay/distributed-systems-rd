<?php

namespace App\Handler;

use App\Entity\Voucher;
use Doctrine\ORM\EntityManagerInterface;

class VoucherHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * VoucherHandler constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function persistVoucher(Voucher $voucher)
    {
        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->persist($voucher);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
