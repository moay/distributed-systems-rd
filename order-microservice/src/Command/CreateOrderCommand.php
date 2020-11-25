<?php

namespace App\Command;

use App\DataTransfer\OrderTransfer;
use App\Handler\OrderHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateOrderCommand extends Command
{
    protected static $defaultName = 'order:create';

    /** @var OrderHandler */
    private $orderHandler;

    /**
     * CreateOrderCommand constructor.
     */
    public function __construct(OrderHandler $orderHandler)
    {
        parent::__construct(self::$defaultName);
        $this->orderHandler = $orderHandler;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates an order of a given value')
            ->addArgument('total', InputArgument::OPTIONAL, 'Net total of the order', 150);
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $netTotal = (float) $input->getArgument('total');
        $orderTransfer = OrderTransfer::createForAmount($netTotal);
        $this->orderHandler->createOrder($orderTransfer);

        return self::SUCCESS;
    }
}
