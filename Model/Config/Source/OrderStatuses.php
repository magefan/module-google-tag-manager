<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\Config\Source;

use Magento\Sales\Model\Config\Source\Order\Status as StatusModel;
use Magento\Framework\Option\ArrayInterface;

class OrderStatuses implements ArrayInterface
{
    /**
     * @var StatusModel
     */
    protected $statusModel;

    protected $optionArray;

    /**
     * @param StatusModel $statusModel
     */
    public function __construct(
        StatusModel $statusModel
    ) {
        $this->statusModel = $statusModel;
    }

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        if (!isset($this->optionArray)) {
            $this->optionArray = [];
            $this->optionArray[] = ['value' => __('any'), 'label' => __('Any status')];

            $statuses = $this->statusModel->toOptionArray();
            foreach ($statuses as $status) {
                if (!isset($status['value']) || !isset($status['label'])) {
                    continue;
                }

                if (!$status['value'] || !$status['label']) {
                    continue;
                }

                $this->optionArray[] = ['value' => (string)$status['value'], 'label' => (string)$status['label']];
            }
        }

        return $this->optionArray;
    }
}
