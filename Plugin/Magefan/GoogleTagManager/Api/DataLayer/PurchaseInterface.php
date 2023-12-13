<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Plugin\Magefan\GoogleTagManager\Api\DataLayer;

use Magefan\GoogleTagManager\Api\DataLayer\PurchaseInterface as Subject;
use Magefan\GoogleTagManager\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magefan\GoogleTagManager\Model\TransactionFactory;
use Magefan\GoogleTagManager\Model\TransactionRepository;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;
use Magefan\GoogleTagManager\Model\Transaction\Log;

class PurchaseInterface
{
    /**
     * @var TransactionCollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var Log
     */
    protected $transactionLogger;

    /**
     * PurchaseInterface constructor.
     * @param TransactionCollectionFactory $transactionCollectionFactory
     * @param Log $transactionLogger
     */
    public function __construct(
        TransactionCollectionFactory $transactionCollectionFactory,
        Log $transactionLogger
    ) {
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->transactionLogger = $transactionLogger;
    }

    /**
     * @param Subject $subject
     * @param $proceed
     * @param Order $order
     * @param string $requester
     * @return array|mixed
     */
    public function aroundGet(Subject $subject, $proceed, Order $order, string $requester = '')
    {
        if ($this->isTransactionIdUniqueForRequester($requester, $order)) {
            $this->transactionLogger->logTransaction($order, $requester);
            return $proceed($order, $requester);
        } else {
            return [];
        }
    }

    /**
     * @param string $requester
     * @param string $transactionId
     * @return bool
     */
    protected function isTransactionIdUniqueForRequester(string $requester, Order $order): bool
    {
        $transactionsForRequesterByTransactionId = $this->transactionCollectionFactory->create()->addFieldToFilter(
            'requester',
            $requester
        )->addFieldToFilter(
            'transaction_id',
            (string)$order->getIncrementId()
        )->addFieldToFilter(
            'store_id',
            (int)$order->getStoreId()
        );

        if (count($transactionsForRequesterByTransactionId)) {
            return false;
        }

        return true;
    }
}
