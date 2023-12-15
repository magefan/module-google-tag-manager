<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

class ExportServerContainerButton extends ExportWebContainerButton
{
    /**
     * @var string
     */
    protected $conteinerType = 'server';

    /**
     * @return string
     */
    public function getContainerGenerateUrl()
    {
        return $this->getUrl(
            'mfgoogletagmanagerextra/serverContainer/generate',
            [
                'store_id' => (int)$this->getRequest()->getParam('store') ?: null,
                'website_id' => (int)$this->getRequest()->getParam('website') ?: null
            ]
        );
    }
}