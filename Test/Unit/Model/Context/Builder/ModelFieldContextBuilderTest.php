<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context;

use IgorRain\CodeGenerator\Model\Context\Builder\ModelFieldContextBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ModelFieldContextBuilderTest extends TestCase
{
    /**
     * @var ModelFieldContextBuilder
     */
    private $builder;

    public function setUp(): void
    {
        $this->builder = new ModelFieldContextBuilder();
    }

    public function testBuild(): void
    {
        $context = $this->builder
            ->setName(ModelFieldContextTest::FIELD_NAME)
            ->setType(ModelFieldContextTest::FIELD_TYPE)
            ->setIsPrimary(ModelFieldContextTest::IS_PRIMARY)
            ->build();

        $this->assertEquals(ModelFieldContextTest::FIELD_NAME, $context->getName());
        $this->assertEquals(ModelFieldContextTest::FIELD_TYPE, $context->getType());
        $this->assertEquals(ModelFieldContextTest::IS_PRIMARY, $context->isPrimary());
    }

    public function testBuildWithoutName(): void
    {
        $this->expectExceptionMessage('Field name is not set');
        $this->builder->build();
    }

    public function testSetNameUsingEmptyName(): void
    {
        $this->expectExceptionMessage('Field name is empty');
        $this->builder->setName('');
    }

    public function testSetNameUsingInvalidName(): void
    {
        $this->expectExceptionMessage('Invalid field name Test');
        $this->builder->setName('Test');
    }

    public function testSetTypeUsingInvalidType(): void
    {
        $this->expectExceptionMessage('Unknown field type test');
        $this->builder->setType('test');
    }

    public function testClear(): void
    {
        $this->expectExceptionMessage('Field name is not set');
        $this->builder
            ->setName(ModelFieldContextTest::FIELD_NAME)
            ->setType(ModelFieldContextTest::FIELD_TYPE)
            ->setIsPrimary(ModelFieldContextTest::IS_PRIMARY)
            ->clear()
            ->build();
    }
}
