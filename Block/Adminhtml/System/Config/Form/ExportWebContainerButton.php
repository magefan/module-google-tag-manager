<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ExportWebContainerButton extends Field
{
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'Magefan_GoogleTagManager::system/config/button/export-container-button.phtml';

    /**
     * @var string
     */
    protected $conteinerType = 'web';

    /**
     * Render button
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getConteinderType()
    {
        return $this->conteinerType;
    }

    /**
     * @return string
     */
    public function getContainerGenerateUrl()
    {
        return $this->getUrl(
            'mfgoogletagmanager/webContainer/generate',
            [
                'store_id' => (int)$this->getRequest()->getParam('store') ?: null,
                'website_id' => (int)$this->getRequest()->getParam('website') ?: null
            ]
        );
    }
}
