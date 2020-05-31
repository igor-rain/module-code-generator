<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context;

use IgorRain\CodeGenerator\Model\Context\ClassContext;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Context\ClassContext
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

    /**
     * @param string $serviceName
     * @param string $className
     * @testWith ["vendor1Module1ApiMenuItemRepositoryV1","Vendor1\\Module1Api\\Api\\Menu\\ItemRepositoryInterface"]
     *           ["catalogProductRepositoryV1","Magento\\Catalog\\Api\\Catalog\\ProductRepositoryInterface"]
     */
    public function testGetMagentoServiceName(string $serviceName, string $className): void
    {
        $this->assertEquals($serviceName, self::createContextWithClassName($className)->getMagentoServiceName());
    }

    public function testCreate(): void
    {
        $module = ModuleContextTest::createApiContext();
        $context = ClassContext::create($module, 'Api\\Menu\\ItemRepositoryInterface');
        $this->assertEquals(self::CLASS_NAME, $context->getName());
    }

    public static function createContext(): ClassContext
    {
        return self::createContextWithClassName(self::CLASS_NAME);
    }

    public static function createContextWithClassName(string $className): ClassContext
    {
        return new ClassContext(ModuleContextTest::createApiContext(), $className);
    }
}
