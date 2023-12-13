<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\Transaction;

use Magefan\GoogleTagManager\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magefan\GoogleTagManager\Model\TransactionFactory;
use Magefan\GoogleTagManager\Model\TransactionRepository;
use Magento\Sales\Api\Data\OrderInterface as Order;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

class Log
{

    /**
     * @var TransactionCollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Log constructor.
     * @param TransactionCollectionFactory $transactionCollectionFactory
     * @param TransactionFactory $transactionFactory
     * @param TransactionRepository $transactionRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransactionCollectionFactory $transactionCollectionFactory,
        TransactionFactory $transactionFactory,
        TransactionRepository $transactionRepository,
        LoggerInterface $logger
    ) {
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->logger = $logger;
    }

    /**
     * @param Order $order
     * @param string $requester
     * @return void
     */
    public function logTransaction(Order $order, string $requester)
    {
        $transactionModel = $this->transactionFactory->create();

        $transactionModel->setTransactionId((string)$order->getIncrementId());
        $transactionModel->setStoreId((int)$order->getStoreId());
        $transactionModel->setRequester($requester);

        try {
            $this->transactionRepository->save($transactionModel);
        } catch (CouldNotSaveException $e) {
            $this->logger->log("Magefan_GoogleTagManager error while logging transaction id: " . $order->getIncrementId()
                . ' and requester: ' . $requester);
        }
    }

    /**
     * @param Order $order
     * @param string $requester
     * @return void
     */
    public function isTransactionUnique(Order $order, string $requester): bool
    {
        $transactionsForRequesterByTransactionId = $this->transactionFactory->create()->getCollection()->addFieldToFilter(
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