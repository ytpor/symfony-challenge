<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use DateTime;

class EstimatorService
{
    public const STR_ERROR = 'Unable to determine delivery time';
    public const STR_EXPRESS = 'express';
    public const STR_STANDARD = 'standard';
    public const STR_LOCAL = 'local';
    public const STR_INTERNATIONAL = 'international';
    public const DAY_ERROR = 999;
    /**
     * FIXME Consider putting these in the database
     */
    public const DAY_EXPRESS_LOCAL = 3;
    public const DAY_EXPRESS_INTERNATIONAL = 5;
    public const DAY_STANDARD_LOCAL = 5;
    public const DAY_STANDARD_INTERNATIONAL = 8;
    public const DAY_BUFFER = 1;

    /**
     * Determine the delivery time
     */
    public function deliveryTime(Order $order): string
    {
        $shippingMethod = $order->getShippingMethod();
        $location = $order->getLocation();
        $orderedAt = $order->getOrderedAt();
        $items = $order->getItems();

        if (empty($shippingMethod) || empty($location) || empty($orderedAt) || empty($items)) {
            return self::STR_ERROR;
        }

        /**
         * Minimum number of days required to ship out the order
         * based on shipping method and location.
         */
        $minimumDays = $this->minimumDays($shippingMethod, $location);

        if ($minimumDays == self::DAY_ERROR) {
            return self::STR_ERROR;
        }

        /**
         * Check whether ordered item is in stock.
         * Determine the date the item will be restocked.
         */
        $restockDate = '';
        foreach ($items as $item) {
            $outOfStock = $item->isOutOfStock();
            $restockOn = $item->getRestockOn();

            if (is_bool($outOfStock) === false) {
                return self::STR_ERROR;
            }

            if ($outOfStock) {
                if (empty($restockOn)) {
                    return self::STR_ERROR;
                }
                if (empty($restockDate)) {
                    $restockDate = $restockOn;
                } else {
                    $restockDate = ($restockOn > $restockDate) ? $restockOn : $restockDate;
                }
            }
        }

        /**
         * This will be the earliest lead time
         */
        $earliestDate = $orderedAt->modify("+$minimumDays days");

        /**
         * Determine the longest lead time
         */
        $deliveryDate = ($earliestDate > $restockDate) ? $earliestDate : $restockDate;

        /**
         * Determine the number days from order date
         */
        $receivedOrderOn = new DateTime($orderedAt->format('Y-m-d'));
        $willDeliverOn = new DateTime($deliveryDate->format('Y-m-d'));
        $days = $receivedOrderOn->diff($willDeliverOn)->days;
        $plusBuffer = $days + self::DAY_BUFFER; // add some buffer

        return "Delivery in {$days}-{$plusBuffer} business days";
    }

    public function minimumDays(string $shippingMethod, string $customerLocation): int
    {
        $minimumDays = [
            self::STR_STANDARD => [
                self::STR_LOCAL => self::DAY_STANDARD_LOCAL,
                self::STR_INTERNATIONAL=> self::DAY_STANDARD_INTERNATIONAL,
            ],
            self::STR_EXPRESS => [
                self::STR_LOCAL => self::DAY_EXPRESS_LOCAL,
                self::STR_INTERNATIONAL=> self::DAY_EXPRESS_INTERNATIONAL,
            ],
        ];

        $days = $minimumDays[$shippingMethod][$customerLocation];

        if (!empty($days)) {
            return $days;
        }

        return self::DAY_ERROR;
    }

    /**
     * This is used to populate a dummy Order with 2 OrderItem
     */
    public function dummyOrder(array $data): Order
    {
        $order = new Order();

        if (isset($data['shipping_method'])) {
            $order->setShippingMethod($data['shipping_method']);
        }

        if (isset($data['location'])) {
            $order->setLocation($data['location']);
        }

        if (isset($data['ordered_at'])) {
            $order->setOrderedAt($data['ordered_at']);
        }

        $item = new OrderItem();

        if (isset($data['items'][0]['product'])) {
            $item->setProduct($data['items'][0]['product']);
        }

        if (isset($data['items'][0]['out_of_stock'])) {
            $item->setOutOfStock($data['items'][0]['out_of_stock']);
        }

        if (isset($data['items'][0]['restock_on'])) {
            $item->setRestockOn($data['items'][0]['restock_on']);
        }

        $order->addItem($item);

        $item = new OrderItem();

        if (isset($data['items'][1]['product'])) {
            $item->setProduct($data['items'][1]['product']);
        }

        if (isset($data['items'][1]['out_of_stock'])) {
            $item->setOutOfStock($data['items'][1]['out_of_stock']);
        }

        if (isset($data['items'][1]['restock_on'])) {
            $item->setRestockOn($data['items'][1]['restock_on']);
        }

        $order->addItem($item);

        return $order;
    }
}
