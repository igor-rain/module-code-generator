<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context;

use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Context\ModuleContext
 */
class ModuleContextTest extends TestCase
{
    public const MODULE_NAME = 'Vendor1_Module1';
    public const MODULE_PATH = '/tmp/module';
    public const MODULE_VERSION = '1.2.3';

    public const MODULE_API_NAME = 'Vendor1_Module1Api';
    public const MODULE_API_PATH = '/tmp/module-api';
    public const MODULE_API_VERSION = '0.0.1';

    public const MODULE_GRAPH_QL_NAME = 'Vendor1_Module1GraphQl';
    public const MODULE_GRAPH_QL_PATH = '/tmp/module-graph-ql';
    public const MODULE_GRAPH_QL_VERSION = '1.0.1';

    public function testGetName(): void
    {
        $this->assertEquals(self::MODULE_NAME, self::createContext()->getName());
        $this->assertEquals(self::MODULE_API_NAME, self::createApiContext()->getName());
    }

    public function testGetPath(): void
    {
        $this->assertEquals(self::MODULE_PATH, self::createContext()->getPath());
        $this->assertEquals(self::MODULE_API_PATH, self::createApiContext()->getPath());
    }

    public function testGetDependencies(): void
    {
        $dependencies = self::createContext()->getDependencies();
        $this->assertCount(1, $dependencies);
        $this->assertEquals(self::MODULE_API_NAME, $dependencies[0]->getName());
        $this->assertEquals([], self::createApiContext()->getDependencies());
    }

    public function testGetVersion(): void
    {
        $this->assertEquals(self::MODULE_VERSION, self::createContext()->getVersion());
        $this->assertEquals(self::MODULE_API_VERSION, self::createApiContext()->getVersion());
    }

    public function testGetComposerPackage(): void
    {
        $this->assertEquals('vendor1/module-module1', self::createContext()->getComposerPackage());
        $this->assertEquals('vendor1/module-module1-api', self::createApiContext()->getComposerPackage());
        $this->assertEquals('abc-def/module-abc3', self::createContextWithName('AbcDef_ABC3')->getComposerPackage());
        $this->assertEquals('abc/module-te-st', self::createContextWithName('ABC_TeSt')->getComposerPackage());
    }

    public function testGetPsr4Prefix(): void
    {
        $this->assertEquals('Vendor1\\Module1\\', self::createContext()->getPsr4Prefix());
        $this->assertEquals('Vendor1\\Module1Api\\', self::createApiContext()->getPsr4Prefix());
    }

    public function testGetModuleDescription(): void
    {
        $this->assertEquals('N/A', self::createContext()->getDescription());
    }

    public static function createContext(): ModuleContext
    {
        return new ModuleContext(
            self::MODULE_NAME,
            self::MODULE_PATH,
            self::MODULE_VERSION,
            [
                self::createApiContext()
            ]
        );
    }

    public static function createApiContext(): ModuleContext
    {
        return new ModuleContext(
            self::MODULE_API_NAME,
            self::MODULE_API_PATH,
            self::MODULE_API_VERSION,
            []
        );
    }

    public static function createGraphQlContext(): ModuleContext
    {
        return new ModuleContext(
            self::MODULE_GRAPH_QL_NAME,
            self::MODULE_GRAPH_QL_PATH,
            self::MODULE_GRAPH_QL_VERSION,
            [
                self::createApiContext()
            ]
        );
    }

    public static function createContextWithName(string $name): ModuleContext
    {
        return new ModuleContext($name, '', '', []);
    }
}
