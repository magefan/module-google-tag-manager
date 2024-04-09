<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\DataLayer;

use Magefan\GoogleTagManager\Api\DataLayer\BeginCheckoutInterface;
use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManager\Api\DataLayer\Cart\ItemInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;

class BeginCheckout extends AbstractDataLayer implements BeginCheckoutInterface
{
    /**
     * @var string
     */
    protected $ecommPageType = 'checkout';

    /**
     * @var ItemInterface
     */
    private $gtmItem;

    /**
     * BeginCheckout constructor.
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
        $value = 0;

        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            $item = $this->gtmItem->get($quoteItem);
            $items[] = $item;
            $value += $item['price'] * $item['quantity'];
        }

        return $this->eventWrap([
            'event' => 'begin_checkout',
            'ecommerce' => [
                'currency' => $this->getCurrentCurrencyCode(),
                'value' => $this->formatPrice($value),
                'coupon' => $quote->getCouponCode() ?: '',
                'items' => $items
            ],
            'customer_identifier' => $quote->getCustomerEmail() ? hash('sha256', (string)$quote->getCustomerEmail()) : ''
        ]);
    }
}
