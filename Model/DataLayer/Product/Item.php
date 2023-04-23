<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\DataLayer\Product;

use Magefan\GoogleTagManager\Api\DataLayer\Product\ItemInterface;
use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magento\Catalog\Model\Product;

class Item extends AbstractDataLayer implements ItemInterface
{
    /**
     * @inheritDoc
     */
    public function get(Product $product): array
    {
        $categoryNames = $this->getCategoryNames($product);
        return array_merge(array_filter([
            'item_id' => $this->getProductAttributeValue($product, $this->config->getProductAttribute()),
            'item_name' => $product->getName(),
            'item_brand' => $this->getProductAttributeValue($product, $this->config->getBrandAttribute()),
            'price' => $this->getPrice($product)
        ]), $categoryNames);
    }
}
