<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\DataLayer;

use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Magefan\GoogleTagManager\Api\DataLayer\Order\ItemInterface;

abstract class AbstractOrder extends AbstractDataLayer
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
    public function get(Order $order, string $requester = ''): array
    {
        if ($order) {
            $items = [];

            $this->setMfChildrenItem($order);

            foreach ($order->getAllVisibleItems() as $item) {
                $items[] = $this->gtmItem->get($item);
            }

            $data = [
                'event' => $this->getEventName(),
                'ecommerce' => [
                    'transaction_id' => $order->getIncrementId(),
                    'value' => $this->getValue($order),
                    'tax' => $this->formatPrice((float)$order->getTaxAmount()),
                    'shipping' => $this->formatPrice((float)$order->getShippingAmount()),
                    'currency' => $this->getCurrentCurrencyCode(),
                    'coupon' => $order->getCouponCode() ?: '',
                    'items' => $items
                ],
                'is_virtual' => (bool)$order->getIsVirtual(),
                'shipping_description' => $order->getShippingDescription(),
                'customer_is_guest' => (bool)$order->getCustomerIsGuest()
            ];

            if ($order->getCustomerId()) {
                $data['customer_id'] = $order->getCustomerId();
            }

            return $this->eventWrap($data);
        }

        return [];
    }

    /**
     * @param Order $order
     * @return float
     */
    protected function getValue(Order $order): float
    {
        $orderValue = (float)$order->getGrandTotal();

        if (!$this->config->isPurchaseTaxEnabled()) {
            $orderValue -= $order->getTaxAmount();
        }

        if (!$this->config->isPurchaseShippingEnabled()) {
            $orderValue -= $order->getShippingAmount();
        }

        return $this->formatPrice((float)$orderValue);
    }

    /**
     * @param $entity
     * @return void
     */
    protected function setMfChildrenItem($entity)
    {
        foreach ($entity->getAllItems() as $childrenItem) {
            if ($parentItemId = $childrenItem->getParentItemId()) {
                foreach ($entity->getAllVisibleItems() as $parentItem) {
                    if ($parentItem->getId() == $parentItemId) {
                        $parentItem->setMfChildrenItem($childrenItem);
                        break;
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    abstract protected function getEventName(): string;

}
