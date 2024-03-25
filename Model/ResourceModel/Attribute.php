<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\View\Element\Context;

class Attribute extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var AttributeCollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @param Context $context
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        AttributeCollectionFactory $attributeCollectionFactory,
        array $data = []
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Retrieve source options
     *
     * @return array
     */
    private function getSourceOptions(): array
    {
        $productAttributes = $this->attributeCollectionFactory->create();
        foreach ($productAttributes as $attribute) {
            $productAttributesOptions[] = ['label' => $attribute->getDefaultFrontendLabel(), 'value' => $attribute->getAttributeCode()];
        }
        $options =
            [
                [
                    'label' => 'Product Attributes',
                    'value' => $productAttributesOptions
                ],
            ];
        return $options;
    }

    /**
     * Generate select HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }
}
