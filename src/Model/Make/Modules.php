<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Make;

use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Context\ModuleContext;

class Modules
{
    /**
     * @var Module
     */
    private $makeModule;
    /**
     * @var ModuleContextBuilder
     */
    private $moduleContextBuilder;

    public function __construct(
        Module $makeModule,
        ModuleContextBuilder $moduleContextBuilder
    ) {
        $this->makeModule = $makeModule;
        $this->moduleContextBuilder = $moduleContextBuilder;
    }

    public function make(string $moduleName): void
    {
        $apiModule = $this->createApiModule($moduleName);
        $module = $this->createModule($moduleName, $apiModule);
        $graphQlModule = $this->createGraphQlModule($moduleName, $apiModule);

        $this->makeModule->make($module);
        $this->makeModule->make($apiModule);
        $this->makeModule->make($graphQlModule);
    }

    protected function createModule(string $moduleName, ModuleContext $apiModule): ModuleContext
    {
        return $this->moduleContextBuilder
            ->setName($moduleName)
            ->setPathAsNew()
            ->addDependency($apiModule)
            ->build();
    }

    protected function createApiModule(string $moduleName): ModuleContext
    {
        return $this->moduleContextBuilder
            ->setName($moduleName . 'Api')
            ->setPathAsNew()
            ->build();
    }

    protected function createGraphQlModule(string $moduleName, ModuleContext $apiModule): ModuleContext
    {
        $magentoGraphQlModule = $this->moduleContextBuilder
            ->setName('Magento_GraphQl')
            ->setVersion('*')
            ->setPathAsExisting()
            ->build();

        return $this->moduleContextBuilder
            ->setName($moduleName . 'GraphQl')
            ->setPathAsNew()
            ->addDependency($apiModule)
            ->addDependency($magentoGraphQlModule)
            ->build();
    }
}
