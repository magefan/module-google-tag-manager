<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magefan\GoogleTagManager\Model\Config;

class GtmCode extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * GtmCode constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getGTMScript(): string
    {
        return $this->config->getGTMScript();
    }

    /**
     * @return string
     */
    public function getGTMNoScript(): string
    {
        return $this->config->getGTMNoScript();
    }

    /**
     * @return string
     */
//    public function getTemplate()
//    {
//        $typeName = str_replace('_', '-', $this->config->getGTMLoaderType());
//        if ('mfgtm.nojscode'  === $this->getNameInLayout()) {
//            $typeName = 'body-' . $typeName;
//        } elseif ('mfgtm.jscode' === $this->getNameInLayout()) {
//            $typeName = 'head-' . $typeName;
//        } else {
//            $typeName = null;
//        }
//
//        if ($typeName) {
//            $loaderTemplate = $this->loaderPool->getLoader($this->config->getGTMLoaderType(), $typeName);
//            if ($loaderTemplate) {
//                return $loaderTemplate;
//            }
//        }
//
//        return parent::getTemplate();
//    }

    /**
     * Check if protect customer data is enabled
     *
     * @return bool
     */
    public function isProtectCustomerDataEnabled(): bool
    {
        return $this->config->isProtectCustomerDataEnabled();
    }

    /**
     * Get current website ID
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getWebsiteId(): int
    {
        return (int)$this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * Init GTM datalayer
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        $content = '';
        if ($this->config->isEnabled()) {
//            parent::_toHtml();
            if ('mfgtm.nojscode'  === $this->getNameInLayout()) {
                $content = $this->config->getGTMScript();
            } elseif ('mfgtm.jscode' === $this->getNameInLayout()) {
                $content = $this->config->getGTMNoScript();
            } else {
                return parent::_toHtml();
            }
            return $content;
        }

        return $content;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}
