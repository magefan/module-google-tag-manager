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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\ResourceModel\GroupRepository;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Sales\Model\Order;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AddressFactory;

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
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var string
     */
    protected $customerGroupCode;

    /**
     * @var string
     */
    protected $ecommPageType = 'other';

    /**
     * @var Product|null
     */
    protected $contextProduct = null;

    /**
     * @var int|null
     */
    protected $contextCustomer = null;

    /**
     * @var Order|null
     */
    protected $contextOrder = null;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var AddressFactory
     */
    protected $addressFactory;

    /**
     * AbstractDataLayer constructor.
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param RequestInterface|null $request
     * @param Registry|null $registry
     * @param Session|null $session
     * @param GroupRepository|null $groupRepository
     * @param ProductRepositoryInterface|null $productRepository
     * @param CustomerRepositoryInterface|null $customerRepository
     * @param AddressFactory|null $addressFactory
     */
    public function __construct(
        Config                      $config,
        StoreManagerInterface       $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        ?RequestInterface            $request = null,
        ?Registry                    $registry = null,
        ?Session                     $session = null,
        ?GroupRepository             $groupRepository = null,
        ?ProductRepositoryInterface    $productRepository = null,
        ?CustomerRepositoryInterface $customerRepository = null,
        ?AddressFactory              $addressFactory = null

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
        $this->productRepository = $productRepository ?: ObjectManager::getInstance()->get(
            ProductRepositoryInterface::class
        );
        $this->customerRepository = $customerRepository ?: ObjectManager::getInstance()->get(
            CustomerRepositoryInterface::class
        );
        $this->addressFactory = $addressFactory ?: ObjectManager::getInstance()->get(
            AddressFactory::class
        );
    }


    /**
     * @return string
     */
    public function getEcommPageType(): string
    {
        if ('other' === $this->ecommPageType) {
            $fullActionName = $this->request->getFullActionName();
            switch ($fullActionName) {
                case 'cms_index_index':
                    $this->ecommPageType = 'home';
                    break;
                case 'catalog_category_view':
                    $this->ecommPageType = 'category';
                    break;
                case 'catalog_product_view':
                    $this->ecommPageType = 'product';
                    break;
                case 'checkout_cart_index':
                    $this->ecommPageType = 'cart';
                    break;
                case 'checkout_index_index':
                    $this->ecommPageType = 'checkout';
                    break;
                case 'contact_index_index':
                    $this->ecommPageType = 'contact';
                    break;
                case 'catalogsearch_result_index':
                    $this->ecommPageType = 'searchresults';
                    break;
                case 'cms_page_view':
                    $this->ecommPageType = 'cmspage';
                    break;
            }
        }

        return $this->ecommPageType;
    }

    /**
     * @param string $ecommPageType
     * @return void
     */
    public function setEcommPageType(string $ecommPageType): void
    {
        $this->ecommPageType = $ecommPageType;
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
            $result['category'] = implode(',', $categoryNames);

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
     * @deprecated
     * @param Product $product
     * @return float
     */
    protected function getPrice(Product $product): float
    {
        $priceInfo = $product->getPriceInfo()->getPrice('final_price')->getAmount();
        $price = $priceInfo->getValue();
        return $this->formatPrice((float)$price);
    }

    /**
     * @param $product
     * @return float
     */
    protected function getProductValue($product): float
    {
        $priceInfo = $product->getPriceInfo()->getPrice('final_price')->getAmount();
        if (!$this->config->isPurchaseTaxEnabled()) {
            $value = $priceInfo->getValue('tax');
        } else {
            $value = $priceInfo->getValue();
        }

        return $this->formatPrice((float)$value);
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
    /*
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
                    /* Do nothing *
                }
            }
        }

        return $this->customerGroupCode;
    }
    */

    /**
     * @param array $data
     * @return array
     */
    public function eventWrap(array $data): array
    {
        if (empty($data)) {
            return $data;
        }

        $data = $this->addMfUniqueEventId($data);
        $data = $this->addEcommPageType($data);
        $data = $this->addCustomEventDimensions($data);

        $data['_clear'] = 'true';

        return $data;
    }

    /**
     * Merge configured event-level custom dimensions into event data using context entities.
     *
     * @param array $data
     * @return array
     */
    protected function addCustomEventDimensions(array $data): array
    {
        $customDimensions = $this->config->getCustomEventDimensions();
        if (!$customDimensions) {
            return $data;
        }

        foreach ($customDimensions as $param => $valueSource) {
            if (strpos($valueSource, '.') === false) {
                continue;
            }
            [$entity, $attribute] = explode('.', $valueSource, 2);

            $value = null;
            if ($this->contextCustomer && is_int($this->contextCustomer)) {
                try {
                    $this->contextCustomer = $this->customerRepository->getById($this->contextCustomer);
                } catch (\Exception $e) {
                    $this->contextCustomer = null;
                }
            }
            switch ($entity) {
                case 'product':
                    if ($this->contextProduct) {
                        $value = $this->getProductAttributeValue($this->contextProduct, $attribute) ?: null;
                    }
                    break;

                case 'customer':
                    if ($this->contextCustomer && $this->contextCustomer->getId()) {
                        $value = $this->getCustomerAttributeValue($attribute);
                    }
                    break;

                case 'address':
                    $value = $this->getAddressAttributeValue($attribute);
                    break;

                case 'order':
                    if ($this->contextOrder) {
                        $value = (string)$this->contextOrder->getData($attribute);
                    }
                    break;

                case 'store':
                    try {
                        $store = $this->storeManager->getStore();
                        if ($attribute === 'currency') {
                            $value = $store->getCurrentCurrencyCode();
                            break;
                        }
                        $value = (string)$store->getData($attribute);
                    } catch (\Exception $e) { // phpcs:ignore
                        /* Do nothing */
                    }
                    break;
            }

            if ($value) {
                $data[$param] = $value;
            }
        }

        return $data;
    }

    /**
     * Get address attribute value from order or customer context.
     *
     * @param string $attribute
     * @return string|null
     * @throws LocalizedException
     */
    protected function getAddressAttributeValue(string $attribute): ?string
    {
        if ($this->contextOrder) {
            $address = $this->contextOrder->getBillingAddress();
            if ($address) {
                return (string)$address->getData($attribute);
            }
        } elseif ($this->contextCustomer && $this->contextCustomer->getId()) {
            $billingId = $this->contextCustomer->getDefaultBilling();
            if ($billingId) {
                $address = $this->addressFactory->create()->load($billingId);
                if ($address->getId()) {
                    return (string)$address->getData($attribute);
                }
            }
        }
        return null;
    }

    /**
     * Get customer attribute value by code, supporting both typed getters and custom attributes.
     *
     * @param string $attribute
     * @return string|null
     */
    protected function getCustomerAttributeValue(string $attribute): ?string
    {
        $customer = $this->contextCustomer;
        $getter = 'get' . str_replace('_', '', ucwords($attribute, '_'));
        if (method_exists($customer, $getter)) {
            $raw = $customer->$getter();
        } else {
            $attr = $customer->getCustomAttribute($attribute);
            $raw = $attr ? $attr->getValue() : null;
        }
        return $raw !== null && $raw !== '' ? (string)$raw : null;
    }

    /**
     * Add custom item dimensions to item data
     *
     * @param $product
     * @param $data
     * @return array
     */
    public function addCustomItemDimensions($product, $data): array
    {
        foreach ($this->config->getCustomItemDimensions() as $param => $attributeCode) {
            if ($value = $this->getProductAttributeValue($product, $attributeCode)) {
                $data[$param] = $value;
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function addMfUniqueEventId(array $data): array
    {
        $hash = hash('sha256', json_encode($data) . microtime());
        $event = isset($data['event']) ? $data['event'] : 'event';
        $eventId = $event . '_' . $hash;

        $data['magefanUniqueEventId'] = $eventId;

        return $data;
    }

    protected function addEcommPageType(array $data): array
    {
        if (!isset($data['ecomm_pagetype'])) {
            $data['ecomm_pagetype'] = $this->getEcommPageType();
        }

        return $data;
    }

}
