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
    public function getGtmScript(): string
    {
        return $this->config->getGtmScript();
    }

    /**
     * @return string
     */
    public function getGtmNoScript(): string
    {
        return $this->config->getGtmNoScript();
    }

    /**
     * @return string
     */
    public function getPublicId(): string
    {
        return $this->config->getPublicId();
    }

    /**
     * @param string|null $storeId
     * @return string
     */
    public function getFormattedGtmScript(string $storeId = null): string
    {
        $partsForRemove = [
            '<!-- Google Tag Manager -->',
            '<!-- End Google Tag Manager -->',
            '<script>',
            '</script>'
        ];
        $gtmScript = $this->getGtmScript();
        if ($gtmScript) {
            foreach ($partsForRemove as $part) {
                $gtmScript = str_replace($part, '', $gtmScript);
            }

            return $gtmScript;
        }

        return '';
    }

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
     * @return string
     */
    protected function _toHtml(): string
    {
        if ($this->config->isEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}
