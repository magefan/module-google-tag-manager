<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\GoogleTagManager\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class ChangePath implements SchemaPatchInterface
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

        $changedConfigurationFields = [
            'mfgoogletagmanager/general/public_id' => 'mfgoogletagmanager/web_container/public_id',
            'mfgoogletagmanager/general/account_id' => 'mfgoogletagmanager/web_container/account_id',
            'mfgoogletagmanager/general/container_id' => 'mfgoogletagmanager/web_container/container_id',
        ];

        foreach ($changedConfigurationFields as $oldPath => $newPath) {
            $connection->update(
                $table,
                ['path' => $newPath],
                ['path = ?' => $oldPath]
            );
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
