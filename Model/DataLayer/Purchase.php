<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\DataLayer;

use Magefan\GoogleTagManager\Api\DataLayer\PurchaseInterface;

class Purchase extends AbstractOrder implements PurchaseInterface
{
    /**
     * @var string
     */
    protected $ecommPageType = 'purchase';

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'purchase';
    }
}
