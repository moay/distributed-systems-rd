<?php

namespace App\Controller;

use App\DataTransfer\OrderTransfer;
use App\Entity\Order;
use App\Handler\OrderHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderOverviewController extends AbstractController
{
    /**
     * @Route("/", name="overview")
     */
    public function overview()
    {
        $orders = $this->getDoctrine()->getRepository(Order::class)->findAll();

        return $this->render('order_overview.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/add-order", methods={"POST"}, name="add-order")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addOrder(Request $request, OrderHandler $orderHandler)
    {
        $orderTotal = $request->request->get('total', 150);
        if (empty($orderTotal)) {
            $orderTotal = 150;
        }
        $orderTransfer = OrderTransfer::createForAmount($orderTotal);
        $orderHandler->createOrder($orderTransfer);

        return $this->redirectToRoute('overview');
    }

    /**
     * @Route("/mark-as-delivered/{id}", name="mark-order-as-delivered")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function markAsDelivered(Order $order, OrderHandler $orderHandler)
    {
        $orderHandler->markOrderAsDelivered($order);

        return $this->redirectToRoute('overview');
    }
}
