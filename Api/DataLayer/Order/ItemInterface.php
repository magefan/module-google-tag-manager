<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Api\DataLayer\Order;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderItemInterface;

interface ItemInterface
{
    /**
     * Get order item
     *
     * @param OrderItemInterface $orderItem
     * @return array
     * @throws NoSuchEntityException
     */
    public function get(OrderItemInterface $orderItem): array;
}
