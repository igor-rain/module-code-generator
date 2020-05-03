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
    public const IS_PRIMARY = false;

    public function testGetName(): void
    {
        $this->assertEquals(self::FIELD_NAME, self::createContext()->getName());
    }

    public function testGetType(): void
    {
        $this->assertEquals(self::FIELD_TYPE, self::createContext()->getType());
    }

    public function testIsPrimary(): void
    {
        $this->assertEquals(self::IS_PRIMARY, self::createContext()->isPrimary());
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
        $context = new ModelFieldContext(
            self::FIELD_NAME,
            $type,
            self::IS_PRIMARY
        );
        $this->assertEquals($phpType, $context->getPhpType());
    }

    public function getGraphQlTypes(): array
    {
        return [
            ['string', 'String'],
            ['text', 'String'],
            ['bool', 'Boolean'],
            ['int', 'Int'],
            ['float', 'Float']
        ];
    }

    /**
     * @dataProvider getGraphQlTypes
     * @param string $type
     * @param string $graphQlType
     */
    public function testGetGraphQlType(string $type, string $graphQlType): void
    {
        $context = new ModelFieldContext(
            self::FIELD_NAME,
            $type,
            self::IS_PRIMARY
        );
        $this->assertEquals($graphQlType, $context->getGraphQlType());
    }

    public static function createContext(): ModelFieldContext
    {
        return new ModelFieldContext(
            self::FIELD_NAME,
            self::FIELD_TYPE,
            self::IS_PRIMARY
        );
    }
}
