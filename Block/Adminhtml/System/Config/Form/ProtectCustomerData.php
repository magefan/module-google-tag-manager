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
        $comment = '
            <strong>Note</strong>: this option works only when default Magento Cookie Restriction Mode is enabled at 
            <a href="' . $url . '" target="_blank">Stores > Configuration > General > Web > Default Cookie Settings</a>.
            If Magento Cookie Restriction Mode is disabled, then GTM JavaScript will be loaded before consent ignoring the "Load GTM Script Before Consent" option.<br/><br/>
            Even if GTM JavaScript is loaded before customer`s consent,
            GTM still waits for consent to send user data related to advertising and analytics.';

        $element->setComment($comment);
        return parent::render($element);
    }
}
