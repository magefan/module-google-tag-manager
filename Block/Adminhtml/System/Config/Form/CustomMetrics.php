<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

use Magefan\GoogleTagManager\Model\ResourceModel\Attribute;
use Magefan\GoogleTagManager\Model\ResourceModel\GetIndex;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Ranges
 */
class CustomMetrics extends AbstractFieldArray
{
    /**
     * @var Attribute
     */
    private $metricsAttribute;

    /**
     * @var GetIndex
     */
    private $getIndex;

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('name', ['label' => __('Name'), 'class' => 'required-entry']);
        $this->addColumn('metrics_attribute', ['label' => __('Attribute'), 'renderer' => $this->getAttribute()
        ]);
        $this->addColumn('metrics_index', ['label' => __('Index'), 'renderer' => $this->getIndex()
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $index = $row->getData('metrics_index');

        if ($index !== null) {
            $options['option_' . $this->getIndex()->calcOptionHash($index)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return Attribute
     * @throws LocalizedException
     */
    private function getAttribute()
    {
        if (!$this->metricsAttribute) {
            $this->metricsAttribute = $this->getLayout()->createBlock(
                Attribute::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->metricsAttribute;
    }

    /**
     * @return GetIndex
     * @throws LocalizedException
     */
    private function getIndex()
    {
        if (!$this->getIndex) {
            $this->getIndex = $this->getLayout()->createBlock(
                GetIndex::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );

        }
        return $this->getIndex;
    }
}
