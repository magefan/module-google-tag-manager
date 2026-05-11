<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

class InfoPlanExtra extends InfoPlan
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
    protected function getSectionsJson(): string
    {
        $sections = json_encode([
            'mfgoogletagmanager_events_purchase_allowed_order_status',
            'mfgoogletagmanager_analytics_measurement_protocol',
            'mfgoogletagmanager_server_container',
            'mfgoogletagmanager_events_purchase_track_admin_orders',
            'mfgoogletagmanager_web_container_gtg_enabled',
            'mfgoogletagmanager_events_custom_dimensions-state',
            'mfgoogletagmanager_attributes_custom_dimensions'
        ]);
        return $sections;
    }

    /**
     * @return string
     */
    protected function getText(): string
    {
        return (string)__("This option is available in <strong>%1</strong> plan only.", $this->getMinPlan());
    }
}
