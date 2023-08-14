<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Plugin\Magefan\GoogleTagManager\Model\DataLayer;

use Magefan\GoogleTagManager\Model\DataLayer\Purchase as Subject;
use Magefan\GoogleTagManager\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magefan\GoogleTagManager\Model\TransactionFactory;
use Magefan\GoogleTagManager\Model\TransactionRepository;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

class Purchase
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
    )
    {
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->logger = $logger;
    }

    /**
     * @param Subject $subject
     * @param $proceed
     * @param Order $order
     * @param string $requester
     * @return array|mixed
     */
    public function aroundGet(Subject $subject, $proceed, Order $order, string $requester = '') {
        if ($this->isTransactionIdUniqueForRequester($requester, $order)) {
            $this->logTransaction($order, $requester);
            return $proceed($order, $requester);
        }
        else {
            return [];
        }
    }

    /**
     * @param string $requester
     * @param string $transactionId
     * @return bool
     */
    protected function isTransactionIdUniqueForRequester(string $requester, Order $order): bool {
        $transactionsForRequesterByTransactionId = $this->transactionCollectionFactory->create()->addFieldToFilter(
            'requester', $requester
        )->addFieldToFilter(
            'transaction_id', (string)$order->getIncrementId()
        )->addFieldToFilter(
            'store_id', (int)$order->getStoreId()
        );

        if (count($transactionsForRequesterByTransactionId)) {
            return false;
        }

        return true;
    }

    /**
     * @param Order $order
     * @param string $requester
     * @return void
     */
    protected function logTransaction(Order $order, string $requester) {
        $transactionModel = $this->transactionFactory->create();

        $transactionModel->setTransactionId((string)$order->getIncrementId());
        $transactionModel->setStoreId((int)$order->getStoreId());
        $transactionModel->setRequester($requester);

        try {
            $this->transactionRepository->save($transactionModel);
        }
        catch (CouldNotSaveException $e) {
            $this->logger->log("Magefan_GoogleTagManager error while logging transaction id: " . $order->getIncrementId()
                . ' and requester: ' . $requester
            );
        }
    }
}