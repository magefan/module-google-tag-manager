<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

use Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form\CustomEventDimensions\CustomerAttributeSelect;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class CustomEventDimensions extends AbstractFieldArray
{
    /**
     * @var CustomerAttributeSelect|null
     */
    private $customerAttributeRenderer = null;

    /**
     * @return CustomerAttributeSelect
     * @throws LocalizedException
     */
    private function getCustomerAttributeRenderer(): CustomerAttributeSelect
    {
        if ($this->customerAttributeRenderer === null) {
            $this->customerAttributeRenderer = $this->getLayout()->createBlock(
                CustomerAttributeSelect::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->customerAttributeRenderer;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn('ga4_param', [
            'label' => __('GA4 Parameter Name'),
            'style' => 'width:160px',
            'class' => 'required-entry',
        ]);
        $this->addColumn('value', [
            'label' => __('Custom Value'),
            'style' => 'width:140px',
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