<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\DataLayer;

use Magefan\GoogleTagManager\Block\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magefan\GoogleTagManager\Api\DataLayer\ViewItemInterface;

class ViewItem extends AbstractDataLayer
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ViewItemInterface
     */
    private $viewItem;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * ViewItem constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param Registry $registry
     * @param ViewItemInterface $viewItem
     * @param ProductRepositoryInterface $productRepository
     * @param array $data
     * @param StoreManagerInterface|null $storeManager
     */
    public function __construct(
        Context $context,
        Config $config,
        Registry $registry,
        ViewItemInterface $viewItem,
        ProductRepositoryInterface $productRepository,
        array $data = [],
        ?StoreManagerInterface $storeManager = null
    ) {
        $this->registry = $registry;
        $this->viewItem = $viewItem;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(StoreManagerInterface::class);
        parent::__construct($context, $config, $data);
    }

    /**
     * Get GTM datalayer for product page
     *
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getDataLayer(): array
    {
        $product = $this->getCurrentProduct();
        $data = $this->viewItem->get($product);
        $child = $this->getPreselectedProduct($product);
        if ($child) {
            $childData = $this->viewItem->get($child);
            if ($child->getVisibility() == Visibility::VISIBILITY_NOT_VISIBLE &&
                isset($data['ecommerce']['items'][0]['item_url'])
            ) {
                $childUrl = $data['ecommerce']['items'][0]['item_url'];

                $delimiter = (false === strpos($childUrl, '?')) ? '?' : '&';
                $childUrl .= $delimiter . 'mfpreselect=' . $child->getId();

                if ('configurable' == $product->getTypeId()) {
                    $attributes = $product->getTypeInstance()->getConfigurableAttributes($product);
                    foreach ($attributes as $attribute) {
                        $attrCode = $attribute->getProductAttribute()->getAttributeCode();
                        $value = $child->getData($attrCode);
                        $delimiter = (false === strpos($childUrl, '?')) ? '?' : '&';
                        $childUrl .= $delimiter . $attrCode . '=' . $value;
                    }
                }

                $childData['ecommerce']['items'][0]['item_url'] = $childUrl;
            }
            $data = $childData;
        }
        return $data;
    }

    /**
     * Get current product
     *
     * @return Product
     */
    private function getCurrentProduct(): Product
    {
        return  $this->registry->registry('current_product');
    }

    /**
     * Get child product preselected via "mfpreselect" if it is a valid child of the current configurable product
     *
     * @param Product|null $product
     * @return Product|null
     */
    private function getPreselectedProduct($product): ?Product
    {
        $productId = (int)$this->_request->getParam('mfpreselect');
        if (!$productId || !$product instanceof Product || 'configurable' !== $product->getTypeId()) {
            return null;
        }

        $childIds = $product->getTypeInstance()->getUsedProductIds($product);
        if (!in_array($productId, $childIds)) {
            return null;
        }

        $store = $this->storeManager->getStore();
        try {
            $child = $this->productRepository->getById($productId, false, $store->getId());
        } catch (NoSuchEntityException $e) {
            return null;
        }

        if ((int)$child->getStatus() !== Status::STATUS_ENABLED
            || !in_array($store->getWebsiteId(), $child->getWebsiteIds())
        ) {
            return null;
        }

        return $child;
    }
}
