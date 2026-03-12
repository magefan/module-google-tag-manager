<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form\CustomDimensions;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class AttributeSelect extends Select
{
    /**
     * @var CollectionFactory
     */
    private $attributes;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionAttributes
     * @param array $data
     */
    public function __construct(
        Context           $context,
        CollectionFactory $collectionAttributes,
        array             $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributes = $collectionAttributes;
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
     * Render block HTML
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

    /**
     * Retrieve options for source selection
     *
     * @return array
     */
    private function getSourceOptions(): array
    {
        $attributes = $this->attributes
            ->create()
            ->addVisibleFilter()
            ->setOrder('frontend_label', 'ASC');

        $attributeArray = [];
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if (in_array($attributeCode, ['sku', 'entity_id'])) {
                continue;
            }
            $attributeArray[] = [
                'label' => $attribute->getFrontendLabel(),
                'value' => $attributeCode,
            ];
        }

        $attributeArray = array_merge([['label' => 'Product ID', 'value' => 'entity_id']], $attributeArray);

        return $attributeArray;
    }
}
