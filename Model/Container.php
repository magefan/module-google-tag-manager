<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;

class Container
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Config
     */
    private $config;

    /**
     * Container constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param Config $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->config = $config;
    }

    /**
     * Generate JSON container
     *
     * @param string|null $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    public function generate(string $storeId = null): array
    {
        $store = $this->storeManager->getStore($storeId);
        $timestamp = (string)$this->dateTime->timestamp();
        $accountId = $this->config->getAccountId($storeId);
        $containerId = $this->config->getContainerId($storeId);
        $publicId = $this->config->getPublicId($storeId);
        $isAnalyticsEnabled = $this->config->isAnalyticsEnabled($storeId);

        return [
            'exportFormatVersion' => 2,
            'exportTime' => $this->dateTime->date('Y-m-d H:i:s'),
            'containerVersion' => [
                'path' => 'accounts/' . $accountId . '/containers/' . $containerId . '/versions/0',
                'accountId' => $accountId,
                'containerId' => $containerId,
                'containerVersionId' => '0',
                'container' => [
                    'path' => 'accounts/' . $accountId . '/containers/' . $containerId,
                    'accountId' => $accountId,
                    'containerId' => $containerId,
                    //'name' => '',
                    'publicId' => $publicId,
                    'usageContext' => [
                        'WEB'
                    ],
                    'fingerprint' => $timestamp,
                    'tagManagerUrl' => 'https://tagmanager.google.com/#/container/accounts/' . $accountId
                        . '/containers/' . $containerId . '/workspaces?apiLink=container'
                ],
                'tag' => $isAnalyticsEnabled ? $this->generateTags($accountId, $containerId, $timestamp, $storeId) : [],
                'trigger' => $isAnalyticsEnabled ? $this->generateTriggers($accountId, $containerId, $timestamp) : [],
                'variable' => [],
                'builtInVariable' => $isAnalyticsEnabled ? [
                    [
                        'accountId' => $accountId,
                        'containerId' => $containerId,
                        'type' => 'EVENT',
                        'name' => 'Event'
                    ]
                ] : [],
                'fingerprint' => $timestamp,
                'tagManagerUrl' => 'https://tagmanager.google.com/#/versions/accounts/' . $accountId
                    . '/containers/' . $containerId . '/versions/0?apiLink=version'
            ]
        ];
    }

    /**
     * Get triggers for container
     *
     * @param string $accountId
     * @param string $containerId
     * @param string $timestamp
     * @return array
     */
    private function generateTriggers(string $accountId, string $containerId, string $timestamp): array
    {
        $triggers[] = [
            'accountId' => $accountId,
            'containerId' => $containerId,
            'triggerId' => '162',
            'name' => 'Magefan GTM - Configuration',
            'type' => 'PAGEVIEW',
            'fingerprint' => $timestamp
        ];

        $triggers[] = [
            'accountId' => $accountId,
            'containerId' => $containerId,
            'triggerId' => '167',
            'name' => 'Magefan GTM - Ecommerce',
            'type' => 'CUSTOM_EVENT',
            'customEventFilter' => [
                [
                    'type' => 'MATCH_REGEX',
                    'parameter' => [
                        [
                            'type' => 'TEMPLATE',
                            'key' => 'arg0',
                            'value' => '{{_event}}'
                        ],
                        [
                            'type' => 'TEMPLATE',
                            'key' => 'arg1',
                            'value' => 'view_item|view_cart|purchase|begin_checkout'
                        ]
                    ]
                ]
            ],
            'fingerprint' => $timestamp
        ];

        $triggerNames = ['View Item', 'View Cart', 'Purchase', 'Begin Checkout'];
        foreach ($triggerNames as $key => $triggerName) {
            $triggers[] = [
                'accountId' => $accountId,
                'containerId' => $containerId,
                'triggerId' => 168 + $key,
                'name' => 'Magefan GTM - ' . $triggerName,
                'type' => 'CUSTOM_EVENT',
                'customEventFilter' => [
                    [
                        'type' => 'EQUALS',
                        'parameter' => [
                            [
                                'type' => 'TEMPLATE',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'TEMPLATE',
                                'key' => 'arg1',
                                'value' => strtolower(str_replace(' ', '_', $triggerName))
                            ]
                        ]
                    ]
                ],
                'fingerprint' => $timestamp
            ];
        }

        return $triggers;
    }

    /**
     * Get tags for container
     *
     * @param string $accountId
     * @param string $containerId
     * @param string $timestamp
     * @param string|null $storeId
     * @return array
     */
    private function generateTags(
        string $accountId,
        string $containerId,
        string $timestamp,
        string $storeId = null
    ): array {
        return [
            [
                'accountId' => $accountId,
                'containerId' => $containerId,
                'tagId' => '163',
                'name' => 'Magefan GA4 - Configuration',
                'type' => 'gaawc',
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'sendPageView',
                        'value' => 'true'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableSendToServerContainer',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'measurementId',
                        'value' => $this->config->getMeasurementId($storeId)
                    ]
                ],
                'fingerprint' => $timestamp,
                'firingTriggerId' => [
                    '162'
                ],
                'tagFiringOption' => 'ONCE_PER_EVENT',
                'monitoringMetadata' => [
                    'type' => 'MAP'
                ],
                'consentSettings' => [
                    'consentStatus' => 'NOT_SET'
                ]
            ],
            [
                'accountId' => $accountId,
                'containerId' => $containerId,
                'tagId' => '169',
                'name' => 'Magefan GA4 - Ecommerce',
                'type' => 'gaawe',
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'sendEcommerceData',
                        'value' => 'true'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'getEcommerceDataFrom',
                        'value' => 'dataLayer'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => '{{Event}}'
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => 'Magefan GA4 - Configuration'
                    ]
                ],
                'fingerprint' => $timestamp,
                'firingTriggerId' => [
                    '167'
                ],
                'tagFiringOption' => 'ONCE_PER_EVENT',
                'monitoringMetadata' => [
                    'type' => 'MAP'
                ],
                'consentSettings' => [
                    'consentStatus' => 'NOT_SET'
                ]
            ]
        ];
    }
}
