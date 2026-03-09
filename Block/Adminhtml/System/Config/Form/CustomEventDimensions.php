<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

use Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form\CustomDimensions\EventDimensionsAttributeSelect;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class CustomEventDimensions extends AbstractFieldArray
{
    /**
     * @var EventDimensionsAttributeSelect|null
     */
    private $attributeRenderer = null;

    /**
     * @return EventDimensionsAttributeSelect
     * @throws LocalizedException
     */
    private function getAttributeRenderer(): EventDimensionsAttributeSelect
    {
        if ($this->attributeRenderer === null) {
            $this->attributeRenderer = $this->getLayout()->createBlock(
                EventDimensionsAttributeSelect::class,
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
        $this->addColumn('value', [
            'label'    => __('Parameter'),
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
        $options = [];
        $value = $row->getData('value');
        if ($value) {
            $options['option_' . $this->getAttributeRenderer()->calcOptionHash($value)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}