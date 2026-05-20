<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\Config\Source;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class CustomerGroups implements OptionSourceInterface
{
    /**
     * @var GroupCollectionFactory
     */
    private $groupCollectionFactory;

    /**
     * @param GroupCollectionFactory $groupCollectionFactory
     */
    public function __construct(
        GroupCollectionFactory $groupCollectionFactory
    ) {
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [
            ['value' => 'all', 'label' => __('All Customer Groups')]
        ];

        $groups = $this->groupCollectionFactory->create();
        foreach ($groups as $group) {
            $options[] = [
                'value' => $group->getId(),
                'label' => $group->getCustomerGroupCode()
            ];
        }

        return $options;
    }
}