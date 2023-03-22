<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\DataLayer;

use Magefan\GoogleTagManager\Block\AbstractDataLayer;

class Other extends AbstractDataLayer
{
    /**
     * Get GTM datalayer for other pages
     *
     * @return array
     */
    protected function getDataLayer(): array
    {
        return [];
    }
}
