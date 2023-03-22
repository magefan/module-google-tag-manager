<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class BrandAttribute implements OptionSourceInterface
{
    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * BrandAttribute constructor.
     *
     * @param AttributeCollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        AttributeCollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $this->options[] = ['value' => '', 'label' => __('--Please Select--')];

            $attributeCollection = $this->attributeCollectionFactory->create();
            $attributeCollection->addVisibleFilter()
                ->setOrder('frontend_label', 'ASC');

            foreach ($attributeCollection->getItems() as $attribute) {
                $label = $attribute->getData('frontend_label');
                $value = $attribute->getData('attribute_code');

                if (array_search($label, array_column($this->options, 'label')) !== false) {
                    $label = $label . ' [' . $value . ']';
                }

                $this->options[] = [
                    'value' => $value,
                    'label' => $label
                ];
            }
        }

        return $this->options;
    }
}
