<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Api\DataLayer;

use Magento\Framework\Session\SessionManager as MagentoSessionManager;

interface SessionManagerInterface
{
    /**
     * @param MagentoSessionManager $session
     * @param array $data
     * @return void
     */
    public function push(MagentoSessionManager $session, array $data): void;

    /**
     * Get GTM datalayer
     *
     * @param MagentoSessionManager $order
     * @return array
     */
    public function get(MagentoSessionManager $session): array;


}
