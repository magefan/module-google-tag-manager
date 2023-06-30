<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\DataLayer;

use Magefan\GoogleTagManager\Api\DataLayer\ViewCartInterface;
use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magefan\GoogleTagManager\Api\DataLayer\Cart\ItemInterface;
use Magento\Store\Model\StoreManagerInterface;

class ViewCart extends AbstractDataLayer implements ViewCartInterface
{
    /**
     * @var ItemInterface
     */
    private $gtmItem;

    /**
     * ViewCart constructor.
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
    public function get(Quote $quote): array
    {
        $items = [];
        $itemsQty = 0;

        foreach ($quote->getAllVisibleItems() as $item) {
            $items[] = $this->gtmItem->get($item);
            $itemsQty += $item->getQty() * 1;
        }

        return $this->eventWrap([
            'event' => 'view_cart',
            'ecommerce' => [
                'currency' => $this->getCurrentCurrencyCode(),
                'value' => $this->formatPrice((float)$quote->getGrandTotal()),
                'items' => $items
            ],
            'items_count' => count($items),
            'items_qty' => $itemsQty,
            'coupon_code' => $quote->getCouponCode() ?: ''
        ]);
    }
}
