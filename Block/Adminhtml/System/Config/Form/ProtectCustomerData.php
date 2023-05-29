<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ProtectCustomerData extends Field
{
    /**
     * Render protect customer data
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $url = $this->getUrl('*/*/*/section/web');
        $comment = 'When enabled, data won\'t be sent to Google, until the customer allows cookies.<br/><br/>
            <strong>Note</strong>, that this option will work only when Cookie Restriction Mode at 
            <a href="' . $url . '" target="_blank">Stores > Configuration > General > Web > Default Cookie Settings</a>
             is enabled.';

        $element->setComment($comment);
        return parent::render($element);
    }
}
