<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\DataLayer;

use Magefan\GoogleTagManager\Block\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magefan\GoogleTagManager\Api\DataLayer\ViewCartInterface;

class ViewCart extends AbstractDataLayer
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var ViewCartInterface
     */
    private $viewCart;

    /**
     * ViewCart constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param CheckoutSession $checkoutSession
     * @param ViewCartInterface $viewCart
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        CheckoutSession $checkoutSession,
        ViewCartInterface $viewCart,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->viewCart = $viewCart;
        parent::__construct($context, $config, $data);
    }

    /**
     * Get GTM datalayer for shopping cart page
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getDataLayer(): array
    {
        $quote = $this->checkoutSession->getQuote();
        return $this->viewCart->get($quote);
    }
}
