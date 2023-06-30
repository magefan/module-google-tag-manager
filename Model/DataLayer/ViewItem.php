<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\DataLayer;

use Magefan\GoogleTagManager\Api\DataLayer\ViewItemInterface;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magento\Store\Model\StoreManagerInterface;
use Magefan\GoogleTagManager\Api\DataLayer\Product\ItemInterface;

class ViewItem extends AbstractDataLayer implements ViewItemInterface
{
    /**
     * @var ItemInterface
     */
    private $gtmItem;

    /**
     * ViewItem constructor.
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ItemInterface $gtmItem
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        ItemInterface $gtmItem
    ) {
        $this->gtmItem = $gtmItem;
        parent::__construct($config, $storeManager, $categoryRepository);
    }

    /**
     * @inheritDoc
     */
    public function get(Product $product): array
    {
        $item = $this->gtmItem->get($product);

        return $this->eventWrap([
            'event' => 'view_item',
            'ecommerce' => [
                'currency' => $this->getCurrentCurrencyCode(),
                'value' => $this->getPrice($product),
                'items' => [
                    $item
                ]
            ]
        ]);
    }
}
