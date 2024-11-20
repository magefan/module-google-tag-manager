<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ScrollDirection implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'vertical', 'label' => __('Vertical')],
            ['value' => 'horizontal', 'label' => __('Horizontal')],
        ];
    }
}
