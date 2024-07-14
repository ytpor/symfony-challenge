<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Service\EstimatorService;
use App\Entity\Order;
use App\Entity\OrderItem;
use DateTime;
use DateTimeImmutable;

/**
 * docker compose run --rm php php bin/phpunit tests/EstimatorServiceTest.php
 */
class EstimatorServiceTest extends TestCase
{
    /**
     * shipping method not supplied
     */
    public function testNoShipping(): void
    {
        // Order details
        $data = [
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemon(),
                $this->orange(),
            ],
        ];

        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals(EstimatorService::STR_ERROR, $deliveryTime);
    }

    /**
     * location not supplied
     */
    public function testNoLocation(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemon(),
                $this->orange(),
            ],
        ];

        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals(EstimatorService::STR_ERROR, $deliveryTime);
    }

    /**
     * order date not supplied
     */
    public function testNoOrderedAt(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_LOCAL,
            'items' => [
                $this->lemon(),
                $this->orange(),
            ],
        ];

        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals(EstimatorService::STR_ERROR, $deliveryTime);
    }

    /**
     * order items not supplied
     */
    public function testNoItems(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
        ];

        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals(EstimatorService::STR_ERROR, $deliveryTime);
    }

    /**
     * ordered item no stock, but restock date not supplied
     */
    public function testNoRestockOn(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemon(),
                $this->orangeNoStock(),
            ],
        ];

        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals(EstimatorService::STR_ERROR, $deliveryTime);
    }

    /**
     * ordered item do not have indication of stock availability
     */
    public function testNoOutOfStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                [
                    'product' => 'Lemon',
                ],
                $this->orangeNoStock(),
            ],
        ];

        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals(EstimatorService::STR_ERROR, $deliveryTime);
    }


    /**
     * express shipping to local
     * all items in stock
     */
    public function testExpressLocal(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_EXPRESS,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemon(),
                $this->orange(),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 3-4 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * express shipping to local
     * lemon no stock
     * orange in stock
     */
    public function testExpressLocalLemonNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_EXPRESS,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemonNoStock((new DateTime())->modify('+10 days')),
                $this->orange(),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 10-11 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * express shipping to local
     * lemon in stock
     * orange no stock
     */
    public function testExpressLocalOrangeNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_EXPRESS,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemon(),
                $this->orangeNoStock((new DateTime())->modify('+2 days')),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 3-4 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * express shipping to local
     * lemon no stock
     * orange no stock
     */
    public function testExpressLocalNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_EXPRESS,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemonNoStock((new DateTime())->modify('+6 days')),
                $this->orangeNoStock((new DateTime())->modify('+5 days')),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 6-7 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * express shipping to international
     * lemon no stock
     * orange in stock
     */
    public function testExpressInternational(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_EXPRESS,
            'location' => EstimatorService::STR_INTERNATIONAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemon(),
                $this->orange(),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 5-6 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * express shipping to international
     * lemon no stock
     * orange in stock
     */
    public function testExpressInternationalLemonNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_EXPRESS,
            'location' => EstimatorService::STR_INTERNATIONAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemonNoStock((new DateTime())->modify('+10 days')),
                $this->orange(),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 10-11 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * express shipping to international
     * lemon in stock
     * orange no stock
     */
    public function testExpressInternationalOrangeNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_EXPRESS,
            'location' => EstimatorService::STR_INTERNATIONAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemon(),
                $this->orangeNoStock((new DateTime())->modify('+2 days')),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 5-6 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * express shipping to international
     * lemon no stock
     * orange no stock
     */
    public function testExpressInternationalNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_EXPRESS,
            'location' => EstimatorService::STR_INTERNATIONAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemonNoStock((new DateTime())->modify('+6 days')),
                $this->orangeNoStock((new DateTime())->modify('+5 days')),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 6-7 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * standard shipping to local
     * all items in stock
     */
    public function testStandardLocal(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemon(),
                $this->orange(),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 5-6 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * standard shipping to local
     * lemon no stock
     * orange in stock
     */
    public function testStandardLocalLemonNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemonNoStock((new DateTime())->modify('+10 days')),
                $this->orange(),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 10-11 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * standard shipping to local
     * lemon in stock
     * orange no stock
     */
    public function testStandardLocalOrangeNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemon(),
                $this->orangeNoStock((new DateTime())->modify('+2 days')),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 5-6 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * standard shipping to local
     * lemon no stock
     * orange no stock
     */
    public function testStandardLocalNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_LOCAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemonNoStock((new DateTime())->modify('+6 days')),
                $this->orangeNoStock((new DateTime())->modify('+5 days')),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 6-7 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * standard shipping to international
     * all items in stock
     */
    public function testStandardInternational(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_INTERNATIONAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemon(),
                $this->orange(),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 8-9 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * standard shipping to international
     * lemon no stock
     * orange in stock
     */
    public function testStandardInternationalLemonNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_INTERNATIONAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemonNoStock((new DateTime())->modify('+10 days')),
                $this->orange(),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 10-11 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * standard shipping to international
     * lemon in stock
     * orange no stock
     */
    public function testStandardInternationalOrangeNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_INTERNATIONAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemon(),
                $this->orangeNoStock((new DateTime())->modify('+2 days')),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 8-9 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * standard shipping to international
     * lemon no stock
     * orange no stock
     */
    public function testStandardInternationalNoStock(): void
    {
        // Order details
        $data = [
            'shipping_method' => EstimatorService::STR_STANDARD,
            'location' => EstimatorService::STR_INTERNATIONAL,
            'ordered_at' => (new DateTimeImmutable()), // ordered today
            'items' => [
                $this->lemonNoStock((new DateTime())->modify('+6 days')),
                $this->orangeNoStock((new DateTime())->modify('+5 days')),
            ],
        ];

        $expectedDeliveryTime = 'Delivery in 8-9 business days';
        $estimator = new EstimatorService();
        $deliveryTime = $estimator->deliveryTime($estimator->dummyOrder($data));

        $this->assertEquals($expectedDeliveryTime, $deliveryTime);
    }

    /**
     * Lemon in stock
     */
    private function lemon(): array
    {
        return [
            'product' => 'Lemon',
            'out_of_stock' => false,
        ];
    }

    /**
     * Lemon no stock
     */
    private function lemonNoStock(Datetime $date = null): array
    {
        return [
            'product' => 'Lemon',
            'out_of_stock' => true,
            'restock_on' => $date,
        ];
    }

    /**
     * Orange in stock
     */
    private function orange(): array
    {
        return [
            'product' => 'Orange',
            'out_of_stock' => false,
        ];
    }

    /**
     * Orange no stock
     */
    private function orangeNoStock(Datetime $date = null): array
    {
        return [
            'product' => 'Lemon',
            'out_of_stock' => true,
            'restock_on' => $date,
        ];
    }
}
