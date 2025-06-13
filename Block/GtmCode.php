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
        $partsForRemove = [
            '<!-- Google Tag Manager -->',
            '<!-- End Google Tag Manager -->',
            '<script>',
            '</script>'
        ];
        $gtmScript = $this->config->getGtmScript();
        if ($gtmScript) {
            foreach ($partsForRemove as $part) {
                $gtmScript = str_replace($part, '', $gtmScript);
            }

            $gtmScript = str_replace($this->getGtmJsUrl(true), $this->getGtmJsUrl(), $gtmScript);

            return $gtmScript;
        }

        return '';
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
     * Check if protect customer data is enabled
     *
     * @return bool
     */
    public function isProtectCustomerDataEnabled(): bool
    {
        return $this->config->isProtectCustomerDataEnabled();
    }


    /**
     * Retrieve true if gtm script should be loaded before customer provice consent.
     *
     * @return bool
     */
    public function isLoadBeforeConsent(): bool
    {
        return $this->config->isLoadBeforeConsent();
    }

    /**
     * Retrieve true if cookie restriction mode enabled
     *
     * @return bool
     */
    public function isCookieRestrictionModeEnabled()
    {
        return $this->config->isCookieRestrictionModeEnabled();
    }

    /**
     * Retrieve true if mf cookie consent extension is enabled
     *
     * @return bool
     */
    public function isMfCookieConsentExtensionEnabled()
    {
        return $this->config->isMfCookieConsentExtensionEnabled();
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

        if ($this->config->isEnabled() /* && $this->getPublicId() */) {
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

    /**
     * Retrieve true if speed optimization is enabled
     *
     * @return bool
     */
    public function isSpeedOptimizationEnabled(): bool
    {
        return (bool)$this->config->isSpeedOptimizationEnabled();
    }


    /**
     * @param $origin
     * @return string
     * @throws NoSuchEntityException
     */
    public function getGtmJsUrl($origin = false): string
    {
        if (!$origin && $this->getConfig()->isGoogleTagGatewayEnabled()) {
            $code = strrev(str_replace('GTM-', '', $this->getPublicId()));
            return rtrim($this->_storeManager->getStore()->getBaseUrl(), '/') . '/' . $code . '/mfgtmproxy/';
        } else {
            return 'https://www.googletagmanager.com/gtm.js';
        }
    }
}
