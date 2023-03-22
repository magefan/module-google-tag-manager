<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Api\DataLayer\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product;

interface ItemInterface
{
    /**
     * Get product item
     *
     * @param Product $product
     * @return array
     * @throws NoSuchEntityException
     */
    public function get(Product $product): array;
}
