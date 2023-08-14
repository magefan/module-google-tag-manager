<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);
namespace Magefan\GoogleTagManager\Block\DataLayer;

use Magefan\GoogleTagManager\Block\DataLayer\Purchase;

/**
 * Custom Block For 2c2p payment method
 */
class PurchaseGetOrderFromRequest extends Purchase
{
    /**
     * @return \Magento\Sales\Model\Order|null
     */
    protected function getOrder() {
        $order = $this->getOrderFactory()->create()->loadByIncrementId($this->getOrderId());

        if (!$order->getId()) {
            return null;
        }
        return $order;
    }

    /**
     * @return mixed
     */
    protected function getOrderFactory() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get(\Magento\Sales\Model\OrderFactory::class);
    }

    /**
     * @return string
     */
    protected function getOrderId() {
        $request = $this->getRequest();

        if ($request) {
            return (string)$request->getParam('order_id');
        }

        return 0;
    }
}