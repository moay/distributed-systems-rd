<?php

namespace App\Command;

use App\Entity\Order;
use App\Handler\OrderHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MarkOrderAsDeliveredCommand extends Command
{
    protected static $defaultName = 'order:mark-as-delivered';

    /** @var OrderHandler */
    private $orderHandler;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * CreateOrderCommand constructor.
     */
    public function __construct(OrderHandler $orderHandler, EntityManagerInterface $entityManager)
    {
        parent::__construct(self::$defaultName);
        $this->orderHandler = $orderHandler;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Marks a random order as delivered');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['deliverySent' => false]);

        if (!$order instanceof Order) {
            $output->writeln('No undelivered order found, you should create one using order:create');

            return self::FAILURE;
        }

        $this->orderHandler->markOrderAsDelivered($order);

        return self::SUCCESS;
    }
}
