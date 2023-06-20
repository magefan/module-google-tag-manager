<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\DataLayer\Cart;

use Magefan\GoogleTagManager\Api\DataLayer\Cart\ItemInterface;
use Magefan\GoogleTagManager\Model\AbstractDataLayer;

class Item extends AbstractDataLayer implements ItemInterface
{
    /**
     * @inheritDoc
     */
    public function get(\Magento\Quote\Model\Quote\Item $quoteItem): array
    {
        $product = $quoteItem->getProduct();
        $categoryNames = $this->getCategoryNames($product);
        return array_merge(array_filter([
            'item_id' => ($this->config->getProductAttribute() == 'sku')
                ? $quoteItem->getSku()
                : $this->getProductAttributeValue($product, $this->config->getProductAttribute()),
            'item_name' => $quoteItem->getName(),
            'discount' => $this->formatPrice((float)$quoteItem->getDiscountAmount()),
            'item_brand' => $this->getProductAttributeValue($product, $this->config->getBrandAttribute()),
            'price' => $this->formatPrice((float)$quoteItem->getPriceInclTax()),
            'quantity' => $quoteItem->getQty() * 1
        ]), $categoryNames);
    }
}
