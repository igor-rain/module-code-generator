<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Make;

use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use IgorRain\CodeGenerator\Model\Locator;
use IgorRain\CodeGenerator\Model\Make\Module;
use IgorRain\CodeGenerator\Model\Make\Modules;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Make\Modules
 */
class ModulesTest extends TestCase
{
    /**
     * @var Locator|MockObject
     */
    private $locator;

    /**
     * @var Module|MockObject
     */
    private $makeModule;
    /**
     * @var Modules
     */
    private $makeModules;

    public function setUp(): void
    {
        $this->locator = $this->createMock(Locator::class);
        $this->makeModule = $this->createMock(Module::class);
        $this->makeModules = new Modules(
            $this->makeModule,
            new ModuleContextBuilder($this->locator)
        );
    }

    public function testMake(): void
    {
        /** @var ModuleContext[] $modules */
        $modules = [];

        $this->makeModule
            ->expects($this->exactly(3))
            ->method('make')
            ->willReturnCallback(static function ($module) use (&$modules) {
                $modules[] = $module;
            });

        $this->locator
            ->expects($this->exactly(3))
            ->method('getNewModulePath')
            ->willReturnCallback(static function ($moduleName) {
                return '/tmp/magento/app/code/' . str_replace('_', '/', $moduleName);
            });

        $this->locator
            ->method('getExistingModulePath')
            ->willReturnCallback(static function ($moduleName) {
                return '/tmp/magento/app/code/' . str_replace('_', '/', $moduleName);
            });

        $this->makeModules->make('Vendor1_Module1');
        $this->assertCount(3, $modules);

        [$module, $apiModule, $graphQlModule] = $modules;

        $this->assertEquals('Vendor1_Module1', $module->getName());
        $this->assertEquals('/tmp/magento/app/code/Vendor1/Module1', $module->getPath());
        $this->assertCount(1, $module->getDependencies());
        $this->assertSame($apiModule, $module->getDependencies()[0]);

        $this->assertEquals('Vendor1_Module1Api', $apiModule->getName());
        $this->assertEquals('/tmp/magento/app/code/Vendor1/Module1Api', $apiModule->getPath());
        $this->assertCount(0, $apiModule->getDependencies());

        $this->assertEquals('Vendor1_Module1GraphQl', $graphQlModule->getName());
        $this->assertEquals('/tmp/magento/app/code/Vendor1/Module1GraphQl', $graphQlModule->getPath());
        $this->assertCount(2, $graphQlModule->getDependencies());
        $this->assertSame($apiModule, $graphQlModule->getDependencies()[0]);
        $this->assertEquals('Magento_GraphQl', $graphQlModule->getDependencies()[1]->getName());
        $this->assertEquals('*', $graphQlModule->getDependencies()[1]->getVersion());
    }
}
