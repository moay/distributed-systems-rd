<?php

namespace App\Controller;

use App\Entity\Voucher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VoucherOverviewController extends AbstractController
{
    /**
     * @Route("/", name="voucher-overview")
     */
    public function overview()
    {
        $vouchers = $this->getDoctrine()->getRepository(Voucher::class)->findAll();

        return $this->render('voucher_overview.html.twig', [
            'vouchers' => $vouchers,
        ]);
    }
}
