<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;
use Magefan\Community\Api\SecureHtmlRendererInterface;
use Magefan\GoogleTagManager\Model\Config;

abstract class AbstractDataLayer extends AbstractBlock
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SecureHtmlRendererInterface
     */
    protected $mfSecureRenderer;

    /**
     * @param Context $context
     * @param Config $config
     * @param array $data
     * @param SecureHtmlRendererInterface|null $mfSecureRenderer
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = [],
        ?SecureHtmlRendererInterface $mfSecureRenderer = null
    ) {
        $this->config = $config;
        $this->mfSecureRenderer = $mfSecureRenderer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(SecureHtmlRendererInterface::class);
        parent::__construct($context, $data);
    }

    /**
     * Get GTM datalayer
     *
     * @return array
     */
    abstract protected function getDataLayer(): array;

    /**
     * Init GTM datalayer
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if ($this->config->isEnabled()) {
            $dataLayer = $this->getDataLayer();
            if ($dataLayer) {
                $json = json_encode($dataLayer);
                $json = preg_replace('/"((getMfGtmCustomerData\(\)\.[^"]+))"/', '$1', $json);
                $intervalSuffix = md5($this->getNameInLayout()) . rand();
                $script = '
                     window.dataLayer = window.dataLayer || [];
                    const mfDataLayerPushInterval' . $intervalSuffix . ' = setInterval(function() {
                        if (!window.dataLayer) return;
            
                        const cookieConsentUpdated = window.dataLayer.some(entry => entry.event === "cookie_consent_update");
                        if (cookieConsentUpdated) { 
                            clearInterval(mfDataLayerPushInterval' . $intervalSuffix . ');
                            window.dataLayer.push(' . $json . ');
                        }
                    }, 200)
                ';
                return $this->mfSecureRenderer->renderTag('script', ['style' => 'display:none'], $script, false);
            }
        }

        return '';
    }
}
