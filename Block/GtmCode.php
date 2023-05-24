<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block;

use Magento\Framework\View\Element\Template;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManager\Model\LoaderPool;

class GtmCode extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoaderPool
     */
    private $loaderPool;

    /**
     * GtmCode constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param LoaderPool $loaderPool
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        LoaderPool $loaderPool,
        array $data = []
    ) {
        $this->config = $config;
        $this->loaderPool = $loaderPool;
        parent::__construct($context, $data);
    }

    /**
     * Get GTM public ID
     *
     * @return string
     */
    public function getPublicId(): string
    {
        return $this->config->getPublicId();
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        $typeName = str_replace('_', '-', $this->config->getGTMLoaderType());
        if ('mfgtm.nojscode'  === $this->getNameInLayout()) {
            $typeName = 'body-' . $typeName;
        } elseif ('mfgtm.jscode' === $this->getNameInLayout()) {
            $typeName = 'head-' . $typeName;
        } else {
            $typeName = null;
        }

        if ($typeName) {
            $loaderTemplate = $this->loaderPool->getLoader($this->config->getGTMLoaderType(), $typeName);
            if ($loaderTemplate) {
                return $loaderTemplate;
            }
        }

        return parent::getTemplate();
    }

    /**
     * Init GTM datalayer
     *
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
