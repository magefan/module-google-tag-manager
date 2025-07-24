<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Plugin\Magento\Customer\CustomerData;

use Magento\Customer\Model\Session;
use Magefan\GoogleTagManager\Model\Config;

class Customer
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Session $session
     */
    public function __construct(
        Session $session,
        Config $config
    ) {
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * @param \Magento\Customer\CustomerData\Customer $subject
     * @param $result
     * @return void
     */
    public function afterGetSectionData(
        \Magento\Customer\CustomerData\Customer $subject,
        $result
    ) {
        return $result;
        if ($this->config->isEnabled()) {
            if ($this->session->getCustomerId()) {
                $result['mf_gtm_customer_identifier'] = hash('sha256', (string)$this->session->getCustomer()->getEmail());
            }
        }

        return $result;
    }
}
