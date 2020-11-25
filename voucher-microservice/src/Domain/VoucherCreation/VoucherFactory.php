<?php

namespace App\Domain\VoucherCreation;

use App\Entity\Voucher;

class VoucherFactory
{
    public static function createVoucher(int $type, string $creationStrategy, float $value, ?string $relatedOrderId): Voucher
    {
        $voucher = new Voucher();
        $voucher->setType($type);
        $voucher->setCreationStrategy($creationStrategy);
        $voucher->setValue($value);

        if ($relatedOrderId) {
            $voucher->setRelatedOrderId($relatedOrderId);
        }

        return $voucher;
    }
}
