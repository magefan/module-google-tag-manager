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
use Magento\Sales\Model\OrderFactory;
class Purchase extends AbstractDataLayer implements PurchaseInterface
{
    /**
     * @var ItemInterface
     */
    private $gtmItem;

    /**
     * @var string
     */
    protected $ecommPageType = 'purchase';
    private $orderFactory;

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
        ItemInterface $gtmItem,
        OrderFactory $orderFactory
    ) {
        $this->gtmItem = $gtmItem;
        $this->orderFactory = $orderFactory;
        parent::__construct($config, $storeManager, $categoryRepository);
    }

    /**
     * @inheritDoc
     */
    public function get(Order $order, string $requester = ''): array
    {
        if ($order) {
            $items = [];
            foreach ($order->getAllVisibleItems() as $item) {
                $items[] = $this->gtmItem->get($item);
            }

            $customerId = $order->getCustomerId();
            $orders = $this->orderFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('entity_id', ['neq' => $order->getId()])
                ->setOrder('created_at', 'DESC');
            $mfGTM_new_customer = true;
            if ($orders->getSize() > 0){
                $lastOrder = $orders->getFirstItem();
                $lastOrderDate = $lastOrder->getCreatedAt();
                $currentDate = date('Y-m-d H:i:s');
                $lastOrderDateTime = strtotime($lastOrderDate);
                $currentDateTime = strtotime($currentDate);
                $daysDiff = floor(($currentDateTime - $lastOrderDateTime) / (60 * 60 * 24));
                if ($daysDiff < 540) {
                    $mfGTM_new_customer = false;
                }
            }

            return $this->eventWrap([
                'event' => 'purchase',
                'ecommerce' => [
                    'transaction_id' => $order->getIncrementId(),
                    'value' => $this->getOrderValue($order),
                    'tax' => $this->formatPrice((float)$order->getTaxAmount()),
                    'shipping' => $this->formatPrice((float)$order->getShippingAmount()),
                    'currency' => $this->getCurrentCurrencyCode(),
                    'coupon' => $order->getCouponCode() ?: '',
                    'items' => $items
                ],
                'is_virtual' => (bool)$order->getIsVirtual(),
                'shipping_description' => $order->getShippingDescription(),
                'customer_is_guest' => (bool)$order->getCustomerIsGuest(),
                'customer_identifier' => hash('sha256', (string)$order->getCustomerEmail()),
                'new_customer' => $mfGTM_new_customer
            ]);
        }

        return [];
    }

    /**
     * @param $order
     * @return float
     */
    protected function getOrderValue($order): float
    {
        $orderValue = (float)$order->getGrandTotal();

        if (!$this->config->isPurchaseTaxEnabled()) {
            $orderValue -= $order->getTaxAmount();
        }

        if (!$this->config->isPurchaseShippingEnabled()) {
            $orderValue -= $order->getShippingAmount();
        }

        return $this->formatPrice($orderValue);
    }
}
