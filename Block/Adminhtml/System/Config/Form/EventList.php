<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class EventList extends Field
{
    public const EVENT_LIST_TEMPLATE = 'system/config/event/list.phtml';

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * EventList constructor.
     *
     * @param Context $context
     * @param Manager $moduleManager
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Context $context,
        Manager $moduleManager,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $data, $secureRenderer);
    }

    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout(): EventList
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::EVENT_LIST_TEMPLATE);
        }
        return $this;
    }

    /**
     * Render event list
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
     * Get the event list and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    /**
     * Retrieve true if GTM Plus is enabled
     *
     * @return bool
     */
    public function isPlusEnabled()
    {
        return (bool)$this->moduleManager->isEnabled('Magefan_GoogleTagManagerPlus');
    }
}
