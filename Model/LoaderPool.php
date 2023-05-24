<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManager\Model;

class LoaderPool
{
    /**
     * Loaders objects
     *
     * @var []
     */
    private $loaders;

    /**
     * LoaderPool constructor.
     * @param array $loaders
//     */
    public function __construct(array $loaders)
    {
        $this->loaders = $loaders;
    }

    /**
     * Retrieve all loaders for type
     * @param $loaderType
     * @return array
     */
    public function getAll(string $loaderType):array
    {
        return $this->loaders[$loaderType] ?? [];
    }

    /**
     * Retrieve template
     * @param string $loaderType
     * @param string $name
     * @return string
     */
    public function getLoader(string $loaderType, string $name):string
    {
        if (isset($this->loaders[$loaderType]) &&
            (is_array($this->loaders[$loaderType]) || $this->loaders[$loaderType] instanceof Countable)
        ) {
            foreach ($this->loaders[$loaderType] as $item) {
                if (isset($item['value']) && $item['value'] == $name) {
                    return $item['template'];
                }
            }
        }
        return '';
    }
}
