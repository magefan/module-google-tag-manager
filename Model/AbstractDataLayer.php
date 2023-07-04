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
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\ResourceModel\GroupRepository;

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
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var string
     */
    protected $customerGroupCode;

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
        CategoryRepositoryInterface $categoryRepository,
        RequestInterface $request = null,
        Registry $registry = null,
        Session $session = null,
        GroupRepository $groupRepository = null
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->request = $request ?: ObjectManager::getInstance()->get(
            RequestInterface::class
        );
        $this->registry = $registry ?: ObjectManager::getInstance()->get(
            Registry::class
        );
        $this->session = $session ?: ObjectManager::getInstance()->get(
            Session::class
        );
        $this->groupRepository = $groupRepository ?: ObjectManager::getInstance()->get(
            GroupRepository::class
        );
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
        $result = [];

        if (!$this->config->getCategoriesAttribute()) {
            return $result;
        }

        if ($productCategory = $this->getCategoryByProduct($product)) {
            $categoryIds = $productCategory->getPathIds();
            $number = 1;
            $categoryNames = [];
            foreach ($categoryIds as $categoryId) {
                $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
                if ($category->getLevel() < 2) {
                    continue;
                }

                $result['item_category' . (($number == 1) ? '' : $number)] = $category->getName();
                $categoryNames[] = $category->getName();
                $number++;
            }
            $result['category'] = implode('/', $categoryNames);

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
        if ('catalog_category_product' == $this->request->getFullActionName()) {
            if ($category = $this->registry->registry('current_category')) {
                return $category;
            }
        }

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
     * @return float
     */
    protected function formatPrice(float $price): float
    {
        return (float)number_format($price, 2, '.', '');
    }

    /**
     * Get product price
     *
     * @param Product $product
     * @return float
     */
    protected function getPrice(Product $product): float
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
            if (is_numeric($result) && !in_array($attributeCode, ['sku', 'entity_id'])) {
                $result = $product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($product);
            }

            if (is_array($result)) {
                $result = implode(', ', $result);
            }
            
            if ($result) {
                return (string)$result;
            }
        }

        return '';
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCustomerGroupCode(): string
    {
        if (null === $this->customerGroupCode) {
            $this->customerGroupCode = 'Guest';
            $customerGroupId = $this->session->getCustomerGroupId();
            if ($customerGroupId) {
                try {
                    $group = $this->groupRepository->getById($customerGroupId);
                    $this->customerGroupCode = (string)$group->getCode();
                } catch (NoSuchEntityException $e) {
                    /* Do nothing */
                }
            }
        }

        return $this->customerGroupCode;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function eventWrap(array $data): array
    {
        if (empty($data)) {
            return $data;
        }

        $data = $this->addCustomerGroup($data);
        $data = $this->addMfUniqueEventId($data);

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function addCustomerGroup(array $data): array
    {
        if (!isset($data['customerGroup'])) {
            $data['customerGroup'] = $this->getCustomerGroupCode();
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function addMfUniqueEventId(array $data): array
    {

        $hash = md5(json_encode($data) . microtime());
        $event = isset($data['event']) ? $data['event'] : 'event';
        $eventId = $event . '_' . $hash;

        $data['magefanUniqueEventId'] = $eventId;

        return $data;
    }
}
