<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\DataLayer\Cart;

use Magefan\GoogleTagManager\Api\DataLayer\Cart\ItemInterface;
use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class Item extends AbstractDataLayer implements ItemInterface
{
    /**
     * @inheritDoc
     */
    public function get(QuoteItem $quoteItem): array
    {
        $product = $this->getItemProduct($quoteItem);
        if (!$product || !$product->getId()) {
            return [];
        }
        $categoryNames = $this->getCategoryNames($product);
        return array_merge([
            'item_id' => ($this->config->getProductAttribute() == 'sku')
                ? $quoteItem->getSku()
                : $this->getProductAttributeValue($product, $this->config->getProductAttribute()),
            'item_name' => $quoteItem->getName(),
            'discount' => $this->formatPrice((float)$quoteItem->getDiscountAmount()),
            'coupon_code' => $quoteItem->getQuote()->getCouponCode() ?: '',
            'item_brand' => $this->getProductAttributeValue($product, $this->config->getBrandAttribute()),
            'price' => $this->getValue($quoteItem),
            'quantity' => $quoteItem->getQty() * 1
        ], $categoryNames, $this->getItemVariant($quoteItem));
    }

    /**
     * @param $quoteItem
     * @return float
     */
    protected function getValue(QuoteItem $quoteItem): float
    {
        if ($this->config->isPurchaseTaxEnabled()) {
            $value = (float)$quoteItem->getPriceInclTax();
            if (!$value) {
                $value = (float)$quoteItem->getPrice();
            }
        } else {
            $value = (float)$quoteItem->getPrice();
        }

        //fix for magento 2.3.2 - module-quote/Model/Quote/Item/Processor.php prepareItem does not set price to quote item
        if (!$value && ($quoteItemProduct = $this->getItemProduct($quoteItem))) {
            return $this->getProductValue($quoteItemProduct);
        } else {
            return $this->formatPrice((float)$value);
        }
    }

    /**
     * @param $quoteItem
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    protected function getItemProduct($quoteItem)
    {
        $product = $quoteItem->getProduct();
        if ('configurable' === $product->getTypeId()) {
            $options = $quoteItem->getOptions();
            if ($options) {
                foreach ($options as $option) {
                    if ($option->getCode() === 'simple_product') {
                        try {
                            $product = $this->productRepository->getById($option->getProductId());
                            break;
                        } catch (NoSuchEntityException $e) {

                        }
                    }
                }
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
        $simpleProductOption = $item->getOptionByCode('simple_product');
        if ($simpleProductOption) {
            $simpleProduct = $simpleProductOption->getProduct();
            if ($simpleProduct) {
                try {
                    $simpleProduct = $this->productRepository->getById($simpleProduct->getId());

                    $attributes = $product->getTypeInstance()
                        ->getConfigurableAttributes($product);

                    foreach ($attributes as $attribute) {
                        $productAttribute = $attribute->getProductAttribute();
                        $label = $productAttribute->getFrontendLabel();
                        $value = $productAttribute->getFrontend()->getValue($simpleProduct);
                        $itemVariant[] = $label . ': ' . $value;
                    }
                } catch (NoSuchEntityException $e) { // phpcs:ignore
                    /* Do nothing */
                }
            }
        }

        if ($itemVariant) {
            return ['item_variant' => implode(',', $itemVariant)];
        }

        return [];
    }
}
