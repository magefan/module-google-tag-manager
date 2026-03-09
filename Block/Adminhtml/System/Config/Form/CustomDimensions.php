<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

use Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form\CustomDimensions\AttributeSelect;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
class CustomDimensions extends AbstractFieldArray
{
    /**
     * @var AttributeSelect|null
     */
    private $attributeRenderer = null;

    /**
     * @return AttributeSelect
     * @throws LocalizedException
     */
    private function getAttributeRenderer(): AttributeSelect
    {
        if ($this->attributeRenderer === null) {
            $this->attributeRenderer = $this->getLayout()->createBlock(
                AttributeSelect::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->attributeRenderer;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn('ga4_param', [
            'label' => __('Dimension name'),
            'class' => 'required-entry',
        ]);
        $this->addColumn('attribute_code', [
            'label' => __('Product Attribute'),
            'renderer' => $this->getAttributeRenderer(),
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $optionExtraAttrs = [];
        $row->setData('option_extra_attrs', $optionExtraAttrs);
    }
}