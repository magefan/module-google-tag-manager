<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\DataLayer;

use Magefan\GoogleTagManager\Api\DataLayer\PurchaseInterface;
use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Magefan\GoogleTagManager\Api\DataLayer\Order\ItemInterface;

class Purchase extends AbstractDataLayer implements PurchaseInterface
{
    /**
     * @var ItemInterface
     */
    private $gtmItem;

    /**
     * Purchase constructor.
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ItemInterface $gtmItem
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        ItemInterface $gtmItem
    ) {
        $this->gtmItem = $gtmItem;
        parent::__construct($config, $storeManager, $categoryRepository);
    }

    /**
     * @inheritDoc
     */
    public function get(Order $order): array
    {
        if ($order) {
            $items = [];
            foreach ($order->getAllVisibleItems() as $item) {
                $items[] = $this->gtmItem->get($item);
            }

            return $this->eventWrap([
                'event' => 'purchase',
                'ecommerce' => [
                    'transaction_id' => $order->getIncrementId(),
                    'value' => $this->formatPrice((float)$order->getGrandTotal()),
                    'tax' => $this->formatPrice((float)$order->getTaxAmount()),
                    'shipping' => $this->formatPrice((float)$order->getShippingAmount()),
                    'currency' => $this->getCurrentCurrencyCode(),
                    'coupon' => $order->getCouponCode() ?: '',
                    'items' => $items
                ],
                'is_virtual' => (bool)$order->getIsVirtual(),
                'shipping_description' => $order->getShippingDescription(),
                'customer_is_guest' => (bool)$order->getCustomerIsGuest()
            ]);
        }

        return [];
    }
}
