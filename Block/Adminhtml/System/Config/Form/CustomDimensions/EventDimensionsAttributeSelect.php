<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form\CustomDimensions;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as ProductAttributeCollectionFactory;
use Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory as CustomerAttributeCollectionFactory;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class EventDimensionsAttributeSelect extends Select
{
    /**
     * @var ProductAttributeCollectionFactory
     */
    private $productAttributesFactory;

    /**
     * @var CustomerAttributeCollectionFactory
     */
    private $customerAttributesFactory;

    /**
     * @param Context $context
     * @param ProductAttributeCollectionFactory $productAttributesFactory
     * @param CustomerAttributeCollectionFactory $customerAttributesFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductAttributeCollectionFactory $productAttributesFactory,
        CustomerAttributeCollectionFactory $customerAttributesFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productAttributesFactory = $productAttributesFactory;
        $this->customerAttributesFactory = $customerAttributesFactory;
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
     * @param string $value
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
     * Build grouped options for all entity types
     *
     * @return array
     */
    private function getSourceOptions(): array
    {
        return [
            ['label' => __('-- Please Select --'), 'value' => ''],
            [
                'label' => __('Product'),
                'value' => $this->getProductOptions(),
            ],
            [
                'label' => __('Order'),
                'value' => $this->getOrderOptions(),
            ],
            [
                'label' => __('Store'),
                'value' => $this->getStoreOptions(),
            ],
            [
                'label' => __('Customer'),
                'value' => $this->getCustomerOptions(),
            ],
            [
                'label' => __('Address'),
                'value' => $this->getAddressOptions(),
            ],
        ];
    }

    /**
     * Load product attribute options
     *
     * @return array
     */
    private function getProductOptions(): array
    {
        $collection = $this->productAttributesFactory->create()
            ->addVisibleFilter()
            ->setOrder('frontend_label', 'ASC');

        $options = [];
        foreach ($collection as $attribute) {
            if (in_array($attribute->getAttributeCode(), ['sku', 'entity_id'])) {
                continue;
            }
            $options[] = [
                'label' => $attribute->getFrontendLabel(),
                'value' => 'product.' . $attribute->getAttributeCode(),
            ];
        }
        return $options;
    }

    /**
     * Load customer attribute options
     *
     * @return array
     */
    private function getCustomerOptions(): array
    {
        $options = [];
        foreach ($this->customerAttributesFactory->create() as $attribute) {
            $options[] = [
                'label' => $attribute->getFrontendLabel(),
                'value' => 'customer.' . $attribute->getAttributeCode(),
            ];
        }
        return $options;
    }

    /**
     * Static address attribute options
     *
     * @return array
     */
    private function getAddressOptions(): array
    {
        return [
            ['label' => __('City'),        'value' => 'address.city'],
            ['label' => __('Country Id'),  'value' => 'address.country_id'],
            ['label' => __('Email'),       'value' => 'address.email'],
            ['label' => __('Fax'),         'value' => 'address.fax'],
            ['label' => __('First name'),  'value' => 'address.firstname'],
            ['label' => __('Last name'),   'value' => 'address.lastname'],
            ['label' => __('Zip/Postal Code'),    'value' => 'address.postcode'],
            ['label' => __('State/Province'), 'value' => 'address.region'],
            ['label' => __('Street'),      'value' => 'address.street'],
            ['label' => __('Telephone'),   'value' => 'address.telephone'],
        ];
    }

    /**
     * Static order attribute options
     *
     * @return array
     */
    private function getOrderOptions(): array
    {
        return [
            ['label' => __('Coupon'),          'value' => 'order.coupon_code'],
            ['label' => __('Order Currency'),  'value' => 'order.base_currency_code'],
            ['label' => __('Order Discount'),  'value' => 'order.base_discount_amount'],
            ['label' => __('Order ID'),        'value' => 'order.id'],
            ['label' => __('Order Tax'),       'value' => 'order.base_tax_amount'],
            ['label' => __('Order Total'),     'value' => 'order.base_grand_total'],
            ['label' => __('Shipping Amount'), 'value' => 'order.base_shipping_amount'],
            ['label' => __('Store Name'),      'value' => 'order.store_name'],
        ];
    }

    /**
     * Static store attribute options
     *
     * @return array
     */
    private function getStoreOptions(): array
    {
        return [
            ['label' => __('Currency'),   'value' => 'store.currency'],
            ['label' => __('Store Code'), 'value' => 'store.code'],
            ['label' => __('Store ID'),   'value' => 'store.store_id'],
            ['label' => __('Store Name'), 'value' => 'store.name'],
        ];

    }
}