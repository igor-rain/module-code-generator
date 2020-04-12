<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context;

use IgorRain\CodeGenerator\Model\Context\ClassContext;
use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ClassContextTest extends TestCase
{
    public const MODULE_NAME = 'Vendor1_Module1Api';

    public const MODULE_PATH = '/tmp/module';

    public const CLASS_NAME = 'Vendor1\\Module1Api\\Api\\Menu\\ItemRepositoryInterface';

    public function testConstructWithEmptyClassName(): void
    {
        $this->expectExceptionMessage('Class name is empty');
        self::createContext('');
    }

    public function testGetName(): void
    {
        $this->assertEquals(self::CLASS_NAME, self::createContext()->getName());
    }

    public function testGetShortName(): void
    {
        $this->assertEquals('ItemRepositoryInterface', self::createContext()->getShortName());
    }

    public function testGetNamespace(): void
    {
        $this->assertEquals('Vendor1\\Module1Api\\Api\\Menu', self::createContext()->getNamespace());
    }

    public function testGetAbsoluteFilePath(): void
    {
        $this->assertEquals(self::MODULE_PATH . '/Api/Menu/ItemRepositoryInterface.php', self::createContext()->getAbsoluteFilePath());
    }

    public function testGetUnitTest(): void
    {
        $this->assertEquals('Vendor1\Module1Api\Test\Unit\Api\Menu\ItemRepositoryInterfaceTest', self::createContext()->getUnitTest()->getName());
    }

    public function testGetIntegrationTest(): void
    {
        $this->assertEquals('Vendor1\Module1Api\Test\Integration\Api\Menu\ItemRepositoryInterfaceTest', self::createContext()->getIntegrationTest()->getName());
    }

    public function testGetFunctionalApiTest(): void
    {
        $this->assertEquals('Vendor1\Module1Api\Test\Api\Api\Menu\ItemRepositoryInterfaceTest', self::createContext()->getApiFunctionalTest()->getName());
    }

    public function testGetMagentoServiceName(): void
    {
        $this->assertEquals('vendor1Module1ApiMenuItemRepositoryV1', self::createContext()->getMagentoServiceName());
    }

    /**
     * @param string $className
     *
     * @return ClassContext
     */
    public static function createContext($className = self::CLASS_NAME): ClassContext
    {
        $moduleContext = new ModuleContext(self::MODULE_NAME, self::MODULE_PATH);

        return new ClassContext($moduleContext, $className);
    }
}
