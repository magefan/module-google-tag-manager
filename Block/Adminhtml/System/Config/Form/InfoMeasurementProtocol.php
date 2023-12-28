<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

class InfoMeasurementProtocol extends InfoPlan
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
        return 'mfgoogletagmanager_analytics_measurement_protocol';
    }

    /**
     * @return string
     */
    protected function getText(): string
    {
        return 'GA4 Measurement Protocol option is available in <strong>Extra</strong> plan only.';
    }

    /**
     * Return info block html
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::render($element);

        $html .= '<div id="ga4mp-disabled" style="display:none; padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">';
        $html .= 'GA4 Measurement Protocol is not available while <a href="#mfgoogletagmanager_server_container-head">GTM Server Container</a> is enabled.';
        $html .= '</div>';

        $html .= '<script>
                require(["jquery", "Magento_Ui/js/modal/alert", "domReady!"], function($, alert){
                    setInterval(function(){
                        if (parseInt($("#mfgoogletagmanager_server_container_enabled").val())) {
                            $("#ga4mp-disabled").show();
                        } else {
                            $("#ga4mp-disabled").hide();
                        }
                    }, 1000);
                });
            </script>';

        return $html;
    }
}
