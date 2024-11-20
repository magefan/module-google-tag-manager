<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ScrollUnits implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'percent', 'label' => __('Percent')],
            ['value' => 'pixels', 'label' => __('Pixels')],
        ];
    }
}
