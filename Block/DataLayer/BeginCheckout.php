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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Context;
use Magefan\GoogleTagManager\Api\DataLayer\BeginCheckoutInterface;

class BeginCheckout extends AbstractDataLayer
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var BeginCheckoutInterface
     */
    private $beginCheckout;

    /**
     * BeginCheckout constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param CheckoutSession $checkoutSession
     * @param BeginCheckoutInterface $beginCheckout
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        CheckoutSession $checkoutSession,
        BeginCheckoutInterface $beginCheckout,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->beginCheckout = $beginCheckout;
        parent::__construct($context, $config, $data);
    }

    /**
     * Get GTM datalayer for checkout page
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getDataLayer(): array
    {
        $quote = $this->checkoutSession->getQuote();
        return $this->beginCheckout->get($quote);
    }
}
