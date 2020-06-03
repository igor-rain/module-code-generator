<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context\Builder;

use IgorRain\CodeGenerator\Model\Context\Builder\ModelContextBuilder;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelFieldContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModuleContextTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Context\Builder\ModelContextBuilder
 */
class ModelContextBuilderTest extends TestCase
{
    /**
     * @var ModelContextBuilder
     */
    private $builder;

    public function setUp(): void
    {
        $this->builder = new ModelContextBuilder();
    }

    public function testBuild(): void
    {
        $module = ModuleContextTest::createContext();
        $apiModule = ModuleContextTest::createApiContext();
        $graphQlModule = ModuleContextTest::createGraphQlContext();
        $field = ModelFieldContextTest::createContext();

        $context = $this->builder
            ->setName(ModelContextTest::MODEL_NAME)
            ->setTableName(ModelContextTest::TABLE_NAME)
            ->setModule($module)
            ->setApiModule($apiModule)
            ->setGraphQlModule($graphQlModule)
            ->addField($field)
            ->build();

        $this->assertEquals(ModelContextTest::MODEL_NAME, $context->getName());
        $this->assertEquals(ModelContextTest::TABLE_NAME, $context->getTableName());
        $this->assertSame($module, $context->getModule());
        $this->assertSame($apiModule, $context->getApiModule());
        $this->assertSame($graphQlModule, $context->getGraphQlModule());
        $this->assertSame([$field], $context->getFields());
    }

    public function testBuildWithoutName(): void
    {
        $this->expectExceptionMessage('Model name is not set');
        $this->builder->build();
    }

    public function testBuildWithoutTableName(): void
    {
        $this->expectExceptionMessage('Table name is not set');
        $this->builder
            ->setName(ModelContextTest::MODEL_NAME)
            ->build();
    }

    public function testBuildWithoutModule(): void
    {
        $this->expectExceptionMessage('Module is not set');
        $this->builder
            ->setName(ModelContextTest::MODEL_NAME)
            ->setTableName(ModelContextTest::TABLE_NAME)
            ->build();
    }

    public function testSetNameUsingEmptyName(): void
    {
        $this->expectExceptionMessage('Model name is empty');
        $this->builder->setName('');
    }

    public function testSetNameUsingInvalidName(): void
    {
        $this->expectExceptionMessage('Invalid model name *');
        $this->builder->setName('*');
    }

    public function testSetTableUsingEmptyTableName(): void
    {
        $this->expectExceptionMessage('Table name is empty');
        $this->builder->setTableName('');
    }

    public function testSetTableUsingInvalidTableName(): void
    {
        $this->expectExceptionMessage('Invalid table name Test');
        $this->builder->setTableName('Test');
    }

    public function testClear(): void
    {
        $this->expectExceptionMessage('Model name is not set');
        $this->builder
            ->setName(ModelContextTest::MODEL_NAME)
            ->setTableName(ModelContextTest::TABLE_NAME)
            ->setModule(ModuleContextTest::createContext())
            ->clear()
            ->build();
    }
}
