<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model;

use Magefan\GoogleTagManager\Model\ResourceModel\Transaction as ResourceTransaction;
use Magefan\GoogleTagManager\Model\TransactionFactory;
use Magefan\GoogleTagManager\Model\Transaction;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class TransactionRepository
{

    /**
     * @var ResourceTransaction
     */
    protected $resource;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @param ResourceTransaction $resource
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        ResourceTransaction $resource,
        TransactionFactory $transactionFactory
    ) {
        $this->resource = $resource;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param Transaction $transaction
     * @return Transaction
     * @throws CouldNotSaveException
     */
    public function save(Transaction $transaction)
    {
        try {
            $this->resource->save($transaction);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the transaction: %1',
                $exception->getMessage()
            ));
        }
        return $transaction;
    }

    /**
     * @param $transactionId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function get($transactionId)
    {
        $transaction = $this->transactionFactory->create();
        $this->resource->load($transaction, $transactionId);
        if (!$transaction->getId()) {
            throw new NoSuchEntityException(__('Transaction with id "%1" does not exist.', $transactionId));
        }
        return $transaction;
    }
}