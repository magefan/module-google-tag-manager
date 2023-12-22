<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Api\Transaction;

interface LogInterface
{
    /**
     * @param mixed $order
     * @param string $requester
     * @return void
     */
    public function logTransaction($order, string $requester);

    /**
     * @param mixed $order
     * @param string $requester
     * @return bool
     */
    public function isTransactionUnique($order, string $requester): bool;
}
