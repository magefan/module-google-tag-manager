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
        return 'Track Admin Orders option is available in <strong>Extra</strong> plan only.';
    }

    /**
     * Return info block html
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->getModuleVersion->execute($this->getModuleName() . $this->getMinPlan())) {
            return '';
        }

        $html = '<script>
                require(["jquery", "Magento_Ui/js/modal/alert", "domReady!"], function($, alert){
                    setInterval(function(){
                        var $plusSection = $("#row_mfgoogletagmanager_events_purchase_track_admin_orders");
                        $plusSection.find(".use-default").css("visibility", "hidden");
                        $plusSection.find("input,select").each(function(){
                            $(this).attr("readonly", "readonly");
                            $(this).removeAttr("disabled");
                            if ($(this).data("gtmdisabled")) return;
                            $(this).data("gtmdisabled", 1);
                            $(this).click(function(){
                                alert({
                                    title: "You cannot change this option.",
                                    content: "' .
            (
            ($this->getMinPlan() == 'Extra')
                ? 'This option is available in <strong>Extra</strong> plan only.'
                : 'This option is available in <strong>Plus or Extra</strong> plans only.'
            )
            . '",
                                    buttons: [{
                                        text: "Upgrade Plan Now",
                                        class: "action primary accept",
                                        click: function () {
                                            window.open("https://magefan.com/magento-2-google-tag-manager/pricing?utm_source=gtm_config&utm_medium=link&utm_campaign=regular");
                                        }
                                    }]
                                });
                            });
                        });
                    }, 1000);
                });
            </script>';

        return $html;
    }
}
