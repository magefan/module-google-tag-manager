<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model;

use Exception;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class AbstractDataLayer
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * AbstractDataLayer constructor.
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get category names
     *
     * @param Product $product
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getCategoryNames(Product $product): array
    {
        /* Temporary disable displaying category names,
         * as it is optional and slows down the website speed
         */
        return [];

        $result = [];

        if ($productCategory = $this->getCategoryByProduct($product)) {
            $categoryIds = $productCategory->getPathIds();
            $number = 1;
            foreach ($categoryIds as $categoryId) {
                $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
                if ($category->getLevel() < 2) {
                    continue;
                }

                $result['item_category' . (($number == 1) ? '' : $number)] = $category->getName();
                $number++;
            }
        }

        return $result;
    }

    /**
     * Get product category
     *
     * @param Product $product
     * @return CategoryInterface|null
     * @throws NoSuchEntityException
     */
    private function getCategoryByProduct(Product $product): ?CategoryInterface
    {
        $productCategory = null;

        $categoryIds = $product->getCategoryIds();

        if ($categoryIds) {
            $level = -1;
            $store = $this->storeManager->getStore();
            $rootCategoryId = $store->getRootCategoryId();

            foreach ($categoryIds as $categoryId) {
                try {
                    $category = $this->categoryRepository->get($categoryId, $store->getId());
                    if ($category->getIsActive()
                        && $category->getLevel() > $level
                        && in_array($rootCategoryId, $category->getPathIds())
                    ) {
                        $level = $category->getLevel();
                        $productCategory = $category;
                    }
                } catch (Exception $e) { // phpcs:ignore
                    /* Do nothing */
                }
            }
        }

        return $productCategory;
    }

    /**
     * Get current currency code
     *
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getCurrentCurrencyCode(): string
    {
        return $this->storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Format price
     *
     * @param float $price
     * @return string
     */
    protected function formatPrice(float $price): string
    {
        return number_format($price, 2, '.', '');
    }

    /**
     * Get product price
     *
     * @param Product $product
     * @return string
     */
    protected function getPrice(Product $product): string
    {
        $priceInfo = $product->getPriceInfo()->getPrice('final_price')->getAmount();
        $price = $priceInfo->getValue();
        return $this->formatPrice($price);
    }

    /**
     * @param Product $product
     * @param string $attributeCode
     * @return string
     */
    protected function getProductAttributeValue(Product $product, ?string $attributeCode): string
    {
        if ($attributeCode) {
            $result = $product->getData($attributeCode);
            if (is_numeric($result) && 'sku' != $attributeCode) {
                $result = $product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($product);
            }
            if ($result) {
                return (string)$result;
            }
        }

        return '';
    }
}
