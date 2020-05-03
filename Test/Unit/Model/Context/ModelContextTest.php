<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\Context\ModelFieldContext;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ModelContextTest extends TestCase
{
    public const MODEL_NAME = 'Menu/Item';
    public const TABLE_NAME = 'menu_item_entity';

    public function testGetName(): void
    {
        $this->assertEquals(self::MODEL_NAME, self::createContext()->getName());
    }

    public function testGetClassDescription(): void
    {
        $this->assertEquals('menu item', self::createContext()->getClassDescription());
    }

    public function testGetVariableName(): void
    {
        $this->assertEquals('menuItem', self::createContext()->getVariableName());
    }

    public function testGetTableName(): void
    {
        $this->assertEquals(self::TABLE_NAME, self::createContext()->getTableName());
    }

    public function testGetTableDescription(): void
    {
        $this->assertEquals('Menu Item Table', self::createContext()->getTableDescription());
    }

    public function testGetPrimaryKey(): void
    {
        $this->assertEquals('entity_id', self::createContext()->getPrimaryKey()->getName());
    }

    public function testGetFields(): void
    {
        $fields = self::createContext()->getFields();
        $fieldsNames = [];
        foreach ($fields as $field) {
            $fieldsNames[] = $field->getName();
        }
        $this->assertEquals([
            'entity_id',
            'sku',
            'name',
            'description',
            'price',
            'attribute_set_id',
            'is_visible'
        ], $fieldsNames);
    }

    public function testGetModule(): void
    {
        $this->assertEquals(ModuleContextTest::MODULE_NAME, self::createContext()->getModule()->getName());
    }

    public function testGetApiModule(): void
    {
        $this->assertEquals(ModuleContextTest::MODULE_API_NAME, self::createContext()->getApiModule()->getName());
    }

    public function testGetGraphQlModule(): void
    {
        $this->assertEquals(ModuleContextTest::MODULE_GRAPH_QL_NAME, self::createContext()->getGraphQlModule()->getName());
    }

    public function testGetEventPrefixName(): void
    {
        $this->assertEquals('module1_menu_item', self::createContext()->getEventPrefixName());
    }

    public function testGetEventObjectName(): void
    {
        $this->assertEquals('menu_item', self::createContext()->getEventObjectName());
    }

    public function testGetAclResourceName(): void
    {
        $this->assertEquals('Vendor1_Module1::menu_item', self::createContext()->getAclResourceName());
    }

    public function testGetModelInterface(): void
    {
        $this->assertEquals('Vendor1\\Module1Api\\Api\\Data\\Menu\\ItemInterface', self::createContext()->getModelInterface()->getName());
    }

    public function testGetModel(): void
    {
        $this->assertEquals('Vendor1\\Module1\\Model\\Menu\\Item', self::createContext()->getModel()->getName());
    }

    public function testGetSearchResultsInterface(): void
    {
        $this->assertEquals('Vendor1\\Module1Api\\Api\\Data\\Menu\\ItemSearchResultsInterface', self::createContext()->getSearchResultsInterface()->getName());
    }

    public function testGetSearchResults(): void
    {
        $this->assertEquals('Vendor1\\Module1\\Model\\Menu\\ItemSearchResults', self::createContext()->getSearchResults()->getName());
    }

    public function testGetRepositoryInterface(): void
    {
        $this->assertEquals('Vendor1\\Module1Api\\Api\\Menu\\ItemRepositoryInterface', self::createContext()->getRepositoryInterface()->getName());
    }

    public function testGetRepository(): void
    {
        $this->assertEquals('Vendor1\\Module1\\Model\\Menu\\ItemRepository', self::createContext()->getRepository()->getName());
    }

    public function testGetResourceModel(): void
    {
        $this->assertEquals('Vendor1\\Module1\\Model\\ResourceModel\\Menu\\Item', self::createContext()->getResourceModel()->getName());
    }

    public function testGetCollection(): void
    {
        $this->assertEquals('Vendor1\\Module1\\Model\\ResourceModel\\Menu\\Item\\Collection', self::createContext()->getCollection()->getName());
    }

    public function testGetGraphQlModelResolver(): void
    {
        $this->assertEquals('Vendor1\\Module1GraphQl\\Model\\Resolver\\Menu\\Item', self::createContext()->getGraphQlModelResolver()->getName());
    }

    public function testGetGraphQlModelDataProvider(): void
    {
        $this->assertEquals('Vendor1\\Module1GraphQl\\Model\\Resolver\\DataProvider\\Menu\\Item', self::createContext()->getGraphQlModelDataProvider()->getName());
    }

    public function testGetFixtureAbsolutePath(): void
    {
        $this->assertEquals(ModuleContextTest::MODULE_PATH . '/Test/Integration/_files/menu_item.php', self::createContext()->getFixtureAbsolutePath('Integration', 'menu_item'));
    }

    public function testGetFixtureRelativePath(): void
    {
        $this->assertEquals('../../../../app/code/Vendor1/Module1/Test/Integration/_files/menu_item.php', self::createContext()->getFixtureRelativePath('Integration', 'menu_item'));
    }

    /**
     * @return ModelContext
     */
    public static function createContext(): ModelContext
    {
        return new ModelContext(
            ModuleContextTest::createContext(),
            ModuleContextTest::createApiContext(),
            ModuleContextTest::createGraphQlContext(),
            self::MODEL_NAME,
            self::TABLE_NAME,
            [
                new ModelFieldContext('entity_id', 'int', true),
                new ModelFieldContext('sku', 'string', false),
                new ModelFieldContext('name', 'string', false),
                new ModelFieldContext('description', 'text', false),
                new ModelFieldContext('price', 'float', false),
                new ModelFieldContext('attribute_set_id', 'int', false),
                new ModelFieldContext('is_visible', 'bool', false),
            ]
        );
    }
}
