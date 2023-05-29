<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Admin configurations information block
 */
class Attention extends \Magefan\Community\Block\Adminhtml\System\Config\Form\Info
{

    /**
     * Return info block html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<div style="padding:10px;background-color:#ffe5e5;border:1px solid #ddd;margin-bottom:7px;">
            <strong>Attention!</strong> Once you change and save the "Web Container" or "Google Analytics 4" settings,
            please don\'t forget to scroll down to the "Export Container" section
            and click the "Generate JSON Container & Download File" button to export container data.
            After you save the file, <a target="_blank" title="Create GTM tags" href="https://magefan.com/blog/add-google-tag-manager-to-magento-2#5-create-gtm-tags">
            import it to your Google Tag Manager container.</a>
        </div>';
    }
}
