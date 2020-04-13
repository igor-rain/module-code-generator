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
    public const FIELD_TYPE = 'string';

    public function testConstructWithEmptyName(): void
    {
        $this->expectExceptionMessage('Field name is empty');
        new ModelFieldContext('', 'string');
    }

    public function testConstructWithInvalidName(): void
    {
        $this->expectExceptionMessage('Invalid field name Test');
        new ModelFieldContext('Test', 'string');
    }

    public function testConstructWithUnknownType(): void
    {
        $this->expectExceptionMessage('Unknown field type test');
        new ModelFieldContext('menu_id', 'test');
    }

    public function testGetName(): void
    {
        $this->assertEquals('attribute_set_id', self::createContext()->getName());
    }

    public function testGetType(): void
    {
        $this->assertEquals('string', self::createContext()->getType());
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

    public function getPhpTypes(): array
    {
        return [
            ['string', 'string'],
            ['text', 'string'],
            ['bool', 'bool'],
            ['int', 'int'],
            ['float', 'float']
        ];
    }

    /**
     * @dataProvider getPhpTypes
     * @param string $type
     * @param string $phpType
     */
    public function testGetPhpType(string $type, string $phpType): void
    {
        $this->assertEquals($phpType, self::createContext(self::FIELD_NAME, $type)->getPhpType());
    }

    public static function createContext(string $name = self::FIELD_NAME, string $type = 'string'): ModelFieldContext
    {
        return new ModelFieldContext($name, $type);
    }
}
