<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model;

use Magento\Framework\Component\ComponentRegistrar;

class Locator
{
    public function getNewModulePath(string $moduleName): string
    {
        $parts = explode('_', $moduleName);
        if (2 !== count($parts)) {
            throw new \RuntimeException('Invalid module name ' . $moduleName);
        }

        return BP . '/app/code/' . implode('/', $parts);
    }

    public function getModulePath(string $moduleName): ?string
    {
        $componentRegistrar = new ComponentRegistrar();

        return $componentRegistrar->getPath('module', $moduleName);
    }
}
