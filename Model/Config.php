<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Config\Model\Config\Backend\Admin\Custom;
use Magento\Framework\Module\Manager as ModuleManager;

class Config
{
    /**
     * General config
     */
    public const XML_PATH_EXTENSION_ENABLED = 'mfgoogletagmanager/general/enabled';

    /**
     * Web Container config
     */
    public const XML_PATH_WEB_CONTAINER_ENABLED = 'mfgoogletagmanager/web_container/enabled';
    public const XML_PATH_INSTALL_GTM = 'mfgoogletagmanager/web_container/install_gtm';
    public const XML_PATH_ACCOUNT_ID = 'mfgoogletagmanager/web_container/account_id';
    public const XML_PATH_CONTAINER_ID = 'mfgoogletagmanager/web_container/container_id';
    public const XML_PATH_WEB_PUBLIC_ID = 'mfgoogletagmanager/web_container/public_id';
    public const XML_PATH_SCRIPT_CONTENT = 'mfgoogletagmanager/web_container/script_content';
    public const XML_PATH_NO_SCRIPT_CONTENT = 'mfgoogletagmanager/web_container/noscript_content';
    public const XML_PATH_GOOGLE_TAG_GATEWAY = 'mfgoogletagmanager/web_container/gtg_enabled';

    /**
     * Analytics config
     */
    public const  XML_PATH_ANALYTICS_ENABLE = 'mfgoogletagmanager/analytics/enable';
    public const  XML_PATH_ANALYTICS_MEASUREMENT_ID = 'mfgoogletagmanager/analytics/measurement_id';

    /**
     * Product attributes config
     */
    public const XML_PATH_ATTRIBUTES_PRODUCT = 'mfgoogletagmanager/attributes/product';
    public const XML_PATH_ATTRIBUTES_BRAND = 'mfgoogletagmanager/attributes/brand';
    public const XML_PATH_ATTRIBUTES_CATEGORIES = 'mfgoogletagmanager/attributes/categories';

    /**
     * Events config
     */
    public const XML_PATH_EVENTS_PURCHASE_TAX_ENABLED = 'mfgoogletagmanager/events/purchase/tax_enabled';
    public const XML_PATH_EVENTS_PURCHASE_SHIPPING_ENABLED = 'mfgoogletagmanager/events/purchase/shipping_enabled';

    /**
     * Customer data protection regulation config
     */
    public const XML_PATH_PROTECT_CUSTOMER_DATA = 'mfgoogletagmanager/customer_data/protect';
    public const XML_PATH_LOAD_BEFORE_CONSENT = 'mfgoogletagmanager/customer_data/load_before_consent';

    /**
     * Speed optimization config
     */
    public const XML_PATH_SPEED_OPTIMIZATION_ENABLED = 'mfgoogletagmanager/page_speed_optimization/enabled';
    public const XML_PATH_THIRD_PARTY_GA = 'mfgoogletagmanager/third_party_ga/enabled';

    /**
     * MF cookie consent extension enabled
     */
    public const XML_PATH_MF_COOKIE_CONSENT_EXTENSION_ENABLED = 'mf_cookie_consent/general/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ModuleManager $moduleManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Retrieve true if module is enabled
     *
     * @param string|null $storeId
     * @return bool
     */
    public function isEnabled(?string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_EXTENSION_ENABLED, $storeId);
    }

    /**
     * Retrieve true if web container enabled
     *
     * @param string|null $storeId
     * @return bool
     */
    public function isWebContainerEnabled(?string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_WEB_CONTAINER_ENABLED, $storeId);
    }

    /**
     * Retrieve GTM account ID
     *
     * @param string|null $storeId
     * @return string
     */
    public function getAccountId(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_ACCOUNT_ID, $storeId));
    }

    /**
     * Retrieve GTM container ID
     *
     * @param string|null $storeId
     * @return string
     */
    public function getContainerId(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_CONTAINER_ID, $storeId));
    }

    /**
     * @param string|null $storeId
     * @return string
     */
    public function getPublicId(?string $storeId = null): string
    {
        if ('use_public_id' === $this->getInstallGtm()) {
            return trim((string)$this->getConfig(self::XML_PATH_WEB_PUBLIC_ID, $storeId));
        } else {
            if ($gtmScript = $this->getGtmScript($storeId)) {
                $pattern = '/GTM-[A-Z0-9]+/';
                $matches = [];
                if (preg_match($pattern, $gtmScript, $matches)) {
                    if (isset($matches[0])) {
                        return trim((string)$matches[0]);
                    }
                }
            }
        }

        return '';
    }

    /**
     * @param string|null $storeId
     * @return string
     */
    public function getGtmScript(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_SCRIPT_CONTENT, $storeId));
    }

    /**
     * @param string|null $storeId
     * @return string
     */
    public function getGtmNoScript(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_NO_SCRIPT_CONTENT, $storeId));
    }

    /**
     * Retrieve true if analytics enabled
     *
     * @param string|null $storeId
     * @return bool
     */
    public function isAnalyticsEnabled(?string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_ANALYTICS_ENABLE, $storeId);
    }

    /**
     * Retrieve Google Analytics measurement ID
     *
     * @param string|null $storeId
     * @return string
     */
    public function getMeasurementId(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_ANALYTICS_MEASUREMENT_ID, $storeId));
    }

    /**
     * @param string|null $storeId
     * @return bool
     */
    public function isPurchaseTaxEnabled(?string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_EVENTS_PURCHASE_TAX_ENABLED, $storeId);
    }

    /**
     * @param string|null $storeId
     * @return bool
     */
    public function isPurchaseShippingEnabled(?string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_EVENTS_PURCHASE_SHIPPING_ENABLED, $storeId);
    }

    /**
     * Retrieve Magento product attribute
     *
     * @param string|null $storeId
     * @return string
     */
    public function getProductAttribute(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_ATTRIBUTES_PRODUCT, $storeId));
    }

    /**
     * Retrieve Magento product brand attribute
     *
     * @param string|null $storeId
     * @return string
     */
    public function getBrandAttribute(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_ATTRIBUTES_BRAND, $storeId));
    }

    /**
     * Retrieve true if protect customer data is enabled
     *
     * @param string|null $storeId
     * @return bool
     */
    public function isProtectCustomerDataEnabled(?string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_PROTECT_CUSTOMER_DATA, $storeId);
    }

    /**
     * Retrieve true if gtm script should be loaded before customer provice consent.
     *
     * @param string|null $storeId
     * @return bool
     */
    public function isLoadBeforeConsent(?string $storeId = null): bool
    {
        return $this->getConfig(self::XML_PATH_LOAD_BEFORE_CONSENT, $storeId) ||
            !$this->isCookieRestrictionModeEnabled($storeId);
    }


    /**
     * Retrieve true if cookie restriction mode enabled
     *
     * @param string|null $storeId
     * @return bool
     */
    public function isCookieRestrictionModeEnabled(?string $storeId = null)
    {
        return (bool)$this->getConfig(Custom::XML_PATH_WEB_COOKIE_RESTRICTION, $storeId);
    }

    /**
     * Retrieve true if mf cookie consent extension is enabled
     *
     * @param string|null $storeId
     * @return bool
     */
    public function isMfCookieConsentExtensionEnabled(?string $storeId = null)
    {
        return $this->moduleManager->isEnabled('Magefan_CookieConsent')
            && $this->getConfig(self::XML_PATH_MF_COOKIE_CONSENT_EXTENSION_ENABLED, $storeId);
    }

    /*
     * Retrieve Magento product categories
     *
     * @param string|null $storeId
     * @return string
     */
    public function getCategoriesAttribute(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_ATTRIBUTES_CATEGORIES, $storeId));
    }

    /**
     * @param string|null $storeId
     * @return int
     */
    public function getInstallGtm(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_INSTALL_GTM, $storeId));
    }

    /**
     * Retrieve store config value
     *
     * @param string $path
     * @param string|null $storeId
     * @return mixed
     */
    public function getConfig(string $path, ?string $storeId = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Retrieve true if speed optimization is enabled
     *
     * @param string|null $storeId
     * @return bool
     */
    public function isSpeedOptimizationEnabled(?string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_SPEED_OPTIMIZATION_ENABLED, $storeId);
    }

    /**
     * @param string|null $storeId
     * @return bool
     */
    public function isThirdPartyGaEnabled(?string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_THIRD_PARTY_GA, $storeId);
    }

    /**
     * @param string|null $storeId
     * @return bool
     */
    public function isGoogleTagGatewayEnabled(?string $storeId = null): bool
    {
        if ($this->moduleManager->isEnabled('Magefan_GoogleTagManagerExtra')) {
            return (bool)$this->getConfig(self::XML_PATH_GOOGLE_TAG_GATEWAY, $storeId);
        }

        return false;
    }
}
