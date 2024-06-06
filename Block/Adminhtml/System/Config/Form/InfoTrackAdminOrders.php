<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

class InfoTrackAdminOrders extends InfoPlan
{
    /**
     * @return string
     */
    protected function getMinPlan(): string
    {
        return 'Extra';
    }

    /**
     * @return string
     */
    protected function getSectionId(): string
    {
        return 'mfgoogletagmanager_events_purchase_track_admin_orders';
    }

    /**
     * @return string
     */
    protected function getText(): string
    {
        return '';
    }
}
