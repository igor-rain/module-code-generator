<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ModelContextTest extends TestCase
{
    public const MODULE_NAME = 'Vendor1_Module1';
    public const MODULE_API_NAME = 'Vendor1_Module1Api';
    public const MODULE_PATH = '/tmp/module';
    public const MODULE_API_PATH = '/tmp/module-api';
    public const RELATIVE_CLASS_NAME = 'Menu/Item';
    public const TABLE_NAME = 'menu_item_entity';

    public function testConstructWithEmptyRelativeClassName(): void
    {
        $this->expectExceptionMessage('Relative class name is empty');

        new ModelContext(
            ModuleContextTest::createContext(self::MODULE_NAME, self::MODULE_PATH),
            ModuleContextTest::createContext(self::MODULE_API_NAME, self::MODULE_API_PATH),
            '',
            self::TABLE_NAME,
            []
        );
    }

    public function testConstructWithEmptyTableName(): void
    {
        $this->expectExceptionMessage('Table name is empty');

        new ModelContext(
            ModuleContextTest::createContext(self::MODULE_NAME, self::MODULE_PATH),
            ModuleContextTest::createContext(self::MODULE_API_NAME, self::MODULE_API_PATH),
            self::RELATIVE_CLASS_NAME,
            '',
            []
        );
    }

    public function testConstructWithoutPrimaryKey(): void
    {
        $this->expectExceptionMessage('Primary key is missing');

        new ModelContext(
            ModuleContextTest::createContext(self::MODULE_NAME, self::MODULE_PATH),
            ModuleContextTest::createContext(self::MODULE_API_NAME, self::MODULE_API_PATH),
            self::RELATIVE_CLASS_NAME,
            self::TABLE_NAME,
            [
                ModelFieldContextTest::createContext('field1'),
                ModelFieldContextTest::createContext('field2'),
            ]
        );
    }

    public function testConstructWithTwoPrimaryKeys(): void
    {
        $field1 = ModelFieldContextTest::createContext('field1');
        $field1->setIsPrimary(true);

        $field2 = ModelFieldContextTest::createContext('field2');
        $field2->setIsPrimary(true);

        $this->expectExceptionMessage('There should be only one primary key');

        new ModelContext(
            ModuleContextTest::createContext(self::MODULE_NAME, self::MODULE_PATH),
            ModuleContextTest::createContext(self::MODULE_API_NAME, self::MODULE_API_PATH),
            self::RELATIVE_CLASS_NAME,
            self::TABLE_NAME,
            [
                $field1,
                $field2,
            ]
        );
    }

    public function testConstructWithInvalidFields(): void
    {
        $this->expectExceptionMessage('Each field should be an instance of ModelFieldContext');

        new ModelContext(
            ModuleContextTest::createContext(self::MODULE_NAME, self::MODULE_PATH),
            ModuleContextTest::createContext(self::MODULE_API_NAME, self::MODULE_API_PATH),
            self::RELATIVE_CLASS_NAME,
            self::TABLE_NAME,
            [
                '111',
                '222',
            ]
        );
    }

    public function testGetRelativeClassName(): void
    {
        $this->assertEquals(self::RELATIVE_CLASS_NAME, self::createContext()->getRelativeClassName());
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
        $primaryKey = self::createContext()->getPrimaryKey();
        $this->assertEquals('entity_id', $primaryKey->getName());
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
            'price',
            'attribute_set_id',
        ], $fieldsNames);
    }

    public function testGetModule(): void
    {
        $this->assertEquals(self::MODULE_NAME, self::createContext()->getModule()->getName());
    }

    public function testGetApiModule(): void
    {
        $this->assertEquals(self::MODULE_API_NAME, self::createContext()->getApiModule()->getName());
    }

    public function testGetEventPrefixName(): void
    {
        $this->assertEquals('module1_menu_item', self::createContext()->getEventPrefixName());
    }

    public function testGetEventObjectName(): void
    {
        $this->assertEquals('menu_item', self::createContext()->getEventObjectName());
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

    public function testGetFixtureAbsolutePath(): void
    {
        $this->assertEquals(self::MODULE_PATH . '/Test/Integration/_files/menu_item.php', self::createContext()->getFixtureAbsolutePath('Integration', 'menu_item'));
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
        $primaryKey = ModelFieldContextTest::createContext('entity_id');
        $primaryKey->setIsPrimary(true);

        return new ModelContext(
            ModuleContextTest::createContext(self::MODULE_NAME, self::MODULE_PATH),
            ModuleContextTest::createContext(self::MODULE_API_NAME, self::MODULE_API_PATH),
            self::RELATIVE_CLASS_NAME,
            self::TABLE_NAME,
            [
                $primaryKey,
                ModelFieldContextTest::createContext('sku'),
                ModelFieldContextTest::createContext('name'),
                ModelFieldContextTest::createContext('price'),
                ModelFieldContextTest::createContext('attribute_set_id'),
            ]
        );
    }
}
