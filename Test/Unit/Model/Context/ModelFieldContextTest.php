<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context;

use IgorRain\CodeGenerator\Model\Context\ModelFieldContext;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ModelFieldContextTest extends TestCase
{
    public const FIELD_NAME = 'attribute_set_id';

    public function testConstructWithEmptyName(): void
    {
        $this->expectExceptionMessage('Field name is empty');
        new ModelFieldContext('');
    }

    public function testConstructWithInvalidName(): void
    {
        $this->expectExceptionMessage('Invalid field name Test');
        new ModelFieldContext('Test');
    }

    public function testGetName(): void
    {
        $this->assertEquals('attribute_set_id', self::createContext()->getName());
    }

    public function testGetIsPrimary(): void
    {
        $this->assertFalse(self::createContext()->getIsPrimary());
    }

    public function testSetIsPrimary(): void
    {
        $context = self::createContext();
        $context->setIsPrimary(true);
        $this->assertTrue($context->getIsPrimary());
    }

    public function testGetConstantName(): void
    {
        $this->assertEquals('ATTRIBUTE_SET_ID', self::createContext()->getConstantName());
    }

    public function testGetMethodName(): void
    {
        $context = self::createContext();
        $this->assertEquals('getAttributeSetId', $context->getMethodName('get'));
        $this->assertEquals('setAttributeSetId', $context->getMethodName('set'));
    }

    public function testGetVariableName(): void
    {
        $this->assertEquals('attributeSetId', self::createContext()->getVariableName());
    }

    public function testGetDescription(): void
    {
        $this->assertEquals('attribute set id', self::createContext()->getDescription());
    }

    public function testGetDescriptionInTable(): void
    {
        $this->assertEquals('Attribute Set ID', self::createContext()->getDescriptionInTable());
    }

    /**
     * @param string $name
     *
     * @return ModelFieldContext
     */
    public static function createContext($name = self::FIELD_NAME)
    {
        return new ModelFieldContext($name);
    }
}
