<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);
namespace Magefan\GoogleTagManager\Block\DataLayer;

use Magefan\GoogleTagManager\Block\DataLayer\Purchase;

class PurchaseGetOrderFromRequest extends Purchase
{
    /**
     * @return \Magento\Sales\Model\Order|null
     */
    protected function getOrder() {
        $order = $this->getOrderFactory()->create()->loadByIncrementId((int)$_REQUEST['order_id']);
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
}