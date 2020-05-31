<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model;

use IgorRain\CodeGenerator\Model\Locator;
use Magento\Framework\Component\ComponentRegistrar;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Locator
 */
class LocatorTest extends TestCase
{
    public function testGetNewModulePath(): void
    {
        $locator = new Locator();
        $modulePath = $locator->getNewModulePath('Vendor1_Module1');
        $this->assertEquals($modulePath, BP . '/app/code/Vendor1/Module1');
    }

    public function testGetNewModulePathUsingWrongModuleName(): void
    {
        $this->expectExceptionMessage('Invalid module name A_B_C');
        $locator = new Locator();
        $locator->getNewModulePath('A_B_C');
    }

    /**
     * @param string $moduleName
     * @param string $modulePath
     * @testWith ["Vendor1_Module1A","/tmp/vendor1/module1a"]
     */
    public function testGetExistingModulePath(string $moduleName, string $modulePath): void
    {
        ComponentRegistrar::register(ComponentRegistrar::MODULE, $moduleName, $modulePath);
        $locator = new Locator();
        $this->assertEquals($modulePath, $locator->getExistingModulePath($moduleName));
    }

    /**
     * @param string $moduleName
     * @testWith ["Vendor1_Module1B"]
     */
    public function testGetExistingModulePathMissing(string $moduleName): void
    {
        $locator = new Locator();
        $this->assertEquals(null, $locator->getExistingModulePath($moduleName));
    }

    /**
     * @param string $moduleName
     * @param string $modulePath
     * @testWith ["Vendor1_Module1C","/tmp/vendor1/module1c"]
     */
    public function testGetExistingModuleNames(string $moduleName, string $modulePath): void
    {
        ComponentRegistrar::register(ComponentRegistrar::MODULE, $moduleName, $modulePath);
        $locator = new Locator();
        $this->assertContains($moduleName, $locator->getExistingModuleNames());
    }
}
