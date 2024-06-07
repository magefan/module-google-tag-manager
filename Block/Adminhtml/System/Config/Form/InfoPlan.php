<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Block\Adminhtml\System\Config\Form;

abstract class InfoPlan extends \Magefan\Community\Block\Adminhtml\System\Config\Form\Info
{
    /**
     * @return string
     */
    abstract protected function getMinPlan(): string;

    /**
     * @return string
     */
    abstract protected function getSectionId(): string;

    /**
     * @return string
     */
    abstract protected function getText(): string;


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

        $html = '';

        if ($text = $this->getText()) {
            $html .= '<div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">';
            $html .= $text . ' <a style="color: #ef672f; text-decoration: underline;" href="https://magefan.com/magento-2-google-tag-manager/pricing?utm_source=gtm_config&utm_medium=link&utm_campaign=regular" target="_blank">Read more</a>.';
            $html .= '</div>';
        }

        $optionAvailableInText = ($this->getMinPlan() == 'Extra')
            ? 'This option is available in <strong>Extra</strong> plan only.'
            : 'This option is available in <strong>Plus or Extra</strong> plans only.';

        $script = <<<script
                require(["jquery", "Magento_Ui/js/modal/alert", "domReady!"], function($, alert){
                    setInterval(function(){
                        var plusSection = $("#{$this->getSectionId()}-state").parent(".section-config");
                        if (!plusSection.length) {
                            plusSection = $("#{$this->getSectionId()}").parents("tr:first");
                        }
                        plusSection.find(".use-default").css("visibility", "hidden");
                        plusSection.find("input,select").each(function(){
                            $(this).attr("readonly", "readonly");
                            $(this).removeAttr("disabled");
                            if ($(this).data("gtmdisabled")) return;
                            $(this).data("gtmdisabled", 1);
                            $(this).click(function(){
                                alert({
                                    title: "You cannot use this option.",
                                    content: "{$optionAvailableInText}",
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
script;

        $html .= $this->mfSecureRenderer->renderTag('script', [], $script, false);

        return $html;
    }
}
