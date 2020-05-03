<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context;

use IgorRain\CodeGenerator\Model\Context\ClassContext;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ClassContextTest extends TestCase
{
    public const CLASS_NAME = 'Vendor1\\Module1Api\\Api\\Menu\\ItemRepositoryInterface';

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
        $this->assertEquals(ModuleContextTest::MODULE_API_PATH . '/Api/Menu/ItemRepositoryInterface.php', self::createContext()->getAbsoluteFilePath());
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

    public static function createContext(): ClassContext
    {
        return new ClassContext(ModuleContextTest::createApiContext(), self::CLASS_NAME);
    }
}
