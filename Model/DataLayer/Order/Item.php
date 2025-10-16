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
        $product = $this->getItemProduct($orderItem);
        if (!$product || !$product->getId()) {
            return [];
        }
        $categoryNames = $this->getCategoryNames($product);
        return array_merge([
            'item_id' => ($this->config->getProductAttribute() == 'sku')
                ? $orderItem->getSku()
                : $this->getProductAttributeValue($product, $this->config->getProductAttribute()),
            'item_name' => $orderItem->getName(),
            'discount' => $this->formatPrice((float)$orderItem->getDiscountAmount()),
            'item_brand' => $this->getProductAttributeValue($product, $this->config->getBrandAttribute()),
            'price' => $this->getValue($orderItem),
            'quantity' => $orderItem->getQtyOrdered() * 1
        ], $categoryNames, $this->getItemVariant($orderItem));
    }

    /**
     * @param $quoteItem
     * @return float
     */
    protected function getValue(OrderItemInterface $orderItem): float
    {
        if ($this->config->isPurchaseTaxEnabled()) {
            $value = (float)$orderItem->getPriceInclTax();
        } else {
            $value = (float)$orderItem->getPrice();
        }
        return $this->formatPrice((float)$value);
    }

    /**
     * @param $orderItem
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    protected function getItemProduct($orderItem)
    {
        $product = $orderItem->getProduct();
        if ('configurable' === $product->getTypeId()) {
            if ($childItem = $orderItem->getMfChildrenItem()) {
                $product =  $childItem->getProduct();
            }
        }
        return $product;
    }

    /**
     * Get item variant
     *
     * @param $item
     * @return array
     * @throws Exception
     */
    protected function getItemVariant($item): array
    {
        $product = $item->getProduct();

        if (!$product || 'configurable' !== $product->getTypeId()) {
            return [];
        }

        $itemVariant = [];

        $productOptions = $item->getProductOptions();
        if ($productOptions) {
            //Order Item
            if (isset($productOptions['attributes_info']) && is_array($productOptions['attributes_info'])) {
                foreach ($productOptions['attributes_info'] as $attribute) {
                    if (isset($attribute['label']) && isset($attribute['value'])) {
                        $itemVariant[] = $attribute['label'] . ': ' . $attribute['value'];
                    }
                }
            }
        }

        if ($itemVariant) {
            return ['item_variant' => implode(',', $itemVariant)];
        }

        return [];
    }
}
