<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;
use Magefan\GoogleTagManager\Model\Config;

abstract class AbstractDataLayer extends AbstractBlock
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * AbstractDataLayer constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        $this->config = $config;
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
                //style always should be displayed none, since some sliders add a class that makes the script display flex/block
                return '<script style="display: none;">
                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push(' . json_encode($dataLayer) . ');
                </script>';
            }
        }

        return '';
    }
}
