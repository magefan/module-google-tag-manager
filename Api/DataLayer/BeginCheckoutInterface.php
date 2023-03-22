<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Api\DataLayer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;

interface BeginCheckoutInterface
{
    /**
     * Get GTM datalayer
     *
     * @param Quote $quote
     * @return array
     * @throws NoSuchEntityException
     */
    public function get(Quote $quote): array;
}
