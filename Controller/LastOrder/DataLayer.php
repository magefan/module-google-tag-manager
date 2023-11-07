<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\GoogleTagManager\Controller\LastOrder;

use Magento\Framework\App\Action\Context;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManager\Model\DataLayer\Purchase;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;

class DataLayer extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Purchase
     */
    protected $purchaseDataLayer;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * ConfirmOrder constructor.
     * @param Context $context
     * @param Config $config
     * @param Purchase $purchaseDataLayer
     * @param OrderRepositoryInterface $orderRepository
     * @param Session $checkoutSession
     * @param JsonFactory $jsonResultFactory
     */
    public function __construct(
        Context $context,
        Config $config,
        Purchase $purchaseDataLayer,
        OrderRepositoryInterface $orderRepository,
        Session $checkoutSession,
        JsonFactory $jsonResultFactory
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->purchaseDataLayer = $purchaseDataLayer;
        $this->orderRepository = $orderRepository;
        $this->checkoutSession = $checkoutSession;
        $this->jsonResultFactory = $jsonResultFactory;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        if ($this->config->isEnabled()) {
            $orderId = $this->checkoutSession->getLastOrderId();
            try {
                $order = $this->orderRepository->get($orderId);
            } catch (\NoSuchElementException $e) {
                $order = null;
            }
            if ($order && $order->getEntityId()) {
                $dataLayer = $this->purchaseDataLayer->get($order);
                if ($dataLayer) {
                    $result->setData($dataLayer);
                }
            }
        }
        return $result;
    }
}
