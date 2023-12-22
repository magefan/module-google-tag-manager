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
use Magefan\GoogleTagManager\Api\Transaction\LogInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Api\Data\OrderInterface as Order;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

class Log implements LogInterface
{
    /**
     * @var TransactionCollectionFactory
     */
    private $transactionCollectionFactory;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * @param TransactionCollectionFactory $transactionCollectionFactory
     * @param TransactionFactory $transactionFactory
     * @param TransactionRepository $transactionRepository
     * @param OrderFactory $orderFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransactionCollectionFactory $transactionCollectionFactory,
        TransactionFactory $transactionFactory,
        TransactionRepository $transactionRepository,
        OrderFactory $orderFactory,
        LoggerInterface $logger
    ) {
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->orderFactory = $orderFactory;
        $this->logger = $logger;
    }

    /**
     * @param mixed $order
     * @param string $requester
     * @return void
     */
    public function logTransaction($order, string $requester)
    {
        $order = $this->getOrder($order);

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
     * @param mixed $order
     * @param string $requester
     * @return bool
     */
    public function isTransactionUnique($order, string $requester): bool
    {
        $order = $this->getOrder($order);

        $transactionsForRequesterByTransactionId = $this->transactionFactory->create()->getCollection()->addFieldToFilter(
            'requester',
            $requester
        )->addFieldToFilter(
            'transaction_id',
            $order->getIncrementId()
        )->addFieldToFilter(
            'store_id',
            (int)$order->getStoreId()
        );

        if (count($transactionsForRequesterByTransactionId)) {
            return false;
        }

        return true;
    }

    /**
     * @param $order
     * @return Order
     * @throws \Exception
     */
    private function getOrder($order): Order
    {
        if (is_object()) {
            if (!($order instanceof Order)) {
                throw new \Exception('Object is not instance of OrderInterface.');
            }
        } else {
            $order = $this->orderFactory->create()->loadByIncrementId((string)$order);
            if (!$order->getId()) {
                throw new \Exception('Order with such ID does not exist.');
            }
        }

        return $order;
    }
}
