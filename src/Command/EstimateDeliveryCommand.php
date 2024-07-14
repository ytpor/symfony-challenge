<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\EstimatorService;
use App\Entity\Order;
use App\Entity\OrderItem;
use DateTime;
use DateTimeImmutable;

/**
 * Console command used to manually test the estimator service.
 *
 * docker compose run --rm php php bin/console app:estimate-delivery
 */
class EstimateDeliveryCommand extends Command
{
    private $estimator;

    protected static $defaultName = 'app:estimate-delivery';

    public function __construct(EstimatorService $estimator)
    {
        parent::__construct();

        $this->estimator = $estimator;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $order = $this->getFirstOrder();

        $output->writeln('Ordered on: ' . $order->getOrderedAt()->format('Y-m-d'));
        foreach ($order->getItems() as $item) {
            $output->writeln('Product: ' . $item->getProduct());
            $output->writeln('Restock on: ' . $item->getRestockOn()->format('Y-m-d'));
        }

        $estimated = $this->estimator->deliveryTime($order);
        $output->writeln($estimated);

        return Command::SUCCESS;
    }

    private function getFirstOrder(): Order
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_EXPRESS,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()),
            'items' => [
                $this->lemonNoStock((new DateTime())->modify('+4 days')),
                $this->orangeNoStock((new DateTime())->modify('+4 days')),
            ],
        ];

        return $this->estimator->dummyOrder($data);
    }

    private function lemonNoStock(Datetime $date = null): array
    {
        return [
            'product' => 'Lemon',
            'out_of_stock' => true,
            'restock_on' => $date,
        ];
    }

    private function orangeNoStock(Datetime $date = null): array
    {
        return [
            'product' => 'Lemon',
            'out_of_stock' => true,
            'restock_on' => $date,
        ];
    }
}
