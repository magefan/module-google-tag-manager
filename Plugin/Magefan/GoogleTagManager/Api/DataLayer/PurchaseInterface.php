<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Plugin\Magefan\GoogleTagManager\Api\DataLayer;

use Magefan\GoogleTagManager\Api\DataLayer\PurchaseInterface as Subject;
use Magefan\GoogleTagManager\Model\TransactionFactory;
use Magento\Sales\Model\Order;
use Magefan\GoogleTagManager\Model\Transaction\Log as TransactionLog;

class PurchaseInterface
{
    /**
     * @var TransactionLog
     */
    protected $transactionLog;

    /**
     * @param TransactionLog $transactionLog
     */
    public function __construct(
        TransactionLog $transactionLog
    ) {
        $this->transactionLog = $transactionLog;
    }

    /**
     * @param Subject $subject
     * @param $proceed
     * @param Order $order
     * @param string $requester
     * @return array
     */
    public function aroundGet(Subject $subject, $proceed, Order $order, string $requester = ''): array
    {
        if ($this->transactionLog->isTransactionUnique($order, $requester)) {
            $this->transactionLog->logTransaction($order, $requester);
            return $proceed($order, $requester);
        } else {
            return [];
        }
    }
}
