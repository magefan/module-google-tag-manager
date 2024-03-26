<?php

namespace Magefan\GoogleTagManager\Plugin\Magento\Framework\App\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magefan\GoogleTagManager\Model\Config;
class ScopeConfig
{
    /**
     * @var Config
     */
    private $config;

    const PATHS = ['google/adwords/active',
        'google/analytics/active',
        'google/gtag/analytics4/active',
        'google/gtag/adwords/active',
        'googletagmanager/general/active',
        'am_ga4/general/enable',
        'weltpixel_ga4/general/enable',
        'mst_gtm/general/enabled',
        'googletagmanager/general/enabled'
        ];

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    )
    {
        $this->config = $config;
    }

    /**
     * @param ScopeConfigInterface $subject
     * @param $result
     * @param $path
     * @param string $scopeType
     * @param $scopeCode
     * @return mixed|string
     */
    public function afterGetValue(
        ScopeConfigInterface $subject,
        $result,
        $path,
        $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    )
    {
        if (in_array($path, self::PATHS)) {
            if ($this->config->isEnabled() && !$this->config->isThirdPartyGaEnabled()) {
                $result = '0';
            }
        }

        return $result;
    }
}
