<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model\ResourceModel;

use Magento\Framework\View\Element\Context;

class GetIndex extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context                    $context,
        array                      $data = []
    )
    {
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
     * @return array
     */
    private function getSourceOptions(): array
    {
        $options = [];
        $maxIndex = 40;
        for ($i = 1; $i <= $maxIndex; $i++)
        {
            $options[$i] = $i;
        }
        return $options;
    }
}
