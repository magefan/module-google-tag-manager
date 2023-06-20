<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\GoogleTagManager\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class DeletePublicId implements SchemaPatchInterface
{

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * Constructor
     *
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->schemaSetup->startSetup();
        $connection = $this->schemaSetup->getConnection();

        $table = $this->schemaSetup->getTable('core_config_data');

        $publicId = $connection->query("SELECT value FROM `core_config_data` WHERE `path` = 'mfgoogletagmanager/web_container/public_id'")->fetch();

        $jsContent = "<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','" . $publicId['value'] . "');</script>
<!-- End Google Tag Manager -->";

        $noJsContent = '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $publicId['value'] . '"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->';

        $columns = [
            'mfgoogletagmanager/web_container/script_content' => $jsContent,
            'mfgoogletagmanager/web_container/noscript_content' =>$noJsContent
        ];

        foreach ($columns as $path => $value) {
            $connection->insert($table, ['scope' => 'default', 'scope_id' => 0, 'path' => $path, 'value' => $value]);
        }
        $this->schemaSetup->endSetup();
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
