<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\DataLayer\Order;

use Magefan\GoogleTagManager\Api\DataLayer\Order\ItemInterface;
use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magento\Sales\Api\Data\OrderItemInterface;

class Item extends AbstractDataLayer implements ItemInterface
{
    /**
     * @inheritDoc
     */
    public function get(OrderItemInterface $orderItem): array
    {
        $product = $orderItem->getProduct();
        $categoryNames = $this->getCategoryNames($product);
        return array_merge(array_filter([
            'item_id' => ($this->config->getProductAttribute() == 'sku')
                ? $orderItem->getSku()
                : $this->getProductAttributeValue($product, $this->config->getProductAttribute()),
            'item_name' => $orderItem->getName(),
            'discount' => $this->formatPrice((float)$orderItem->getDiscountAmount()),
            'item_brand' => $this->getProductAttributeValue($product, $this->config->getBrandAttribute()),
            'price' => $this->formatPrice((float)$orderItem->getPriceInclTax()),
            'quantity' => $orderItem->getQtyOrdered() * 1
        ]), $categoryNames);
    }
}
