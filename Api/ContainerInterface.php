<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface ContainerInterface
{
    /**
     * Generate JSON container
     *
     * @param string|null $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    public function generate(?string $storeId = null): array;
}
