<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\DataLayer;

use Magefan\GoogleTagManager\Block\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Context;
use Magefan\GoogleTagManager\Api\DataLayer\PurchaseInterface;

class Purchase extends AbstractDataLayer
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var PurchaseInterface
     */
    private $purchase;

    /**
     * Purchase constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param CheckoutSession $checkoutSession
     * @param PurchaseInterface $purchase
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        CheckoutSession $checkoutSession,
        PurchaseInterface $purchase,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->purchase = $purchase;
        parent::__construct($context, $config, $data);
    }

    /**
     * Get GTM datalayer for checkout success page
     *
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getDataLayer(): array
    {
        $order = $this->checkoutSession->getLastRealOrder();
        return $this->purchase->get($order);
    }
}
