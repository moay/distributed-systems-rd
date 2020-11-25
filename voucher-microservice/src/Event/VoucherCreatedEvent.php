<?php

namespace App\Event;

use App\Entity\Voucher;

class VoucherCreatedEvent
{
    /** @var Voucher */
    private $voucher;

    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }

    public function getVoucher(): Voucher
    {
        return $this->voucher;
    }
}
