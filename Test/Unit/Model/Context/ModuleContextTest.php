<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context;

use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ModuleContextTest extends TestCase
{
    public const MODULE_NAME = 'Vendor1_Module1';

    public const MODULE_API_NAME = 'Vendor1_Module1Api';

    public const MODULE_PATH = '/tmp/module';

    public function testConstructWithEmptyName(): void
    {
        $this->expectExceptionMessage('Module name is empty');
        new ModuleContext('', self::MODULE_PATH);
    }

    public function testConstructWithInvalidName(): void
    {
        $this->expectExceptionMessage('Invalid module name test');
        new ModuleContext('test', self::MODULE_PATH);
    }

    public function testConstructWithEmptyPath(): void
    {
        $this->expectExceptionMessage('Module path is empty');
        new ModuleContext(self::MODULE_NAME, '');
    }

    public function testGetName(): void
    {
        $this->assertEquals(self::MODULE_NAME, self::createContext()->getName());
    }

    public function testGetPath(): void
    {
        $this->assertEquals(self::MODULE_PATH, self::createContext()->getPath());
    }

    public function testGetDependencies(): void
    {
        $this->assertEquals([], self::createContext()->getDependencies());
    }

    public function testSetDependencies(): void
    {
        $dependencies = [
            self::createContext('Vendor2_Module1'),
            self::createContext('Vendor2_Module2'),
        ];

        $context = self::createContext();
        $context->setDependencies($dependencies);
        $this->assertEquals($dependencies, $context->getDependencies());
    }

    public function testGetVersion(): void
    {
        $this->assertEquals('0.0.1', self::createContext()->getVersion());
    }

    public function testSetVersion(): void
    {
        $context = self::createContext();
        $context->setVersion('1.2.3');
        $this->assertEquals('1.2.3', $context->getVersion());
    }

    public function testGetComposerPackage(): void
    {
        $context = self::createContext(self::MODULE_API_NAME);
        $this->assertEquals('vendor1/module-module1-api', $context->getComposerPackage());
    }

    public function testGetPsr4Prefix(): void
    {
        $this->assertEquals('Vendor1\\Module1\\', self::createContext()->getPsr4Prefix());
    }

    public function testGetModuleDescription(): void
    {
        $context = self::createContext(self::MODULE_API_NAME);
        $this->assertEquals('Magento module responsible for Vendor1 Module1 Api', $context->getDescription());
    }

    public static function createContext(string $name = self::MODULE_NAME, string $path = self::MODULE_PATH): ModuleContext
    {
        return new ModuleContext($name, $path);
    }
}
