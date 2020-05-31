<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context;

use IgorRain\CodeGenerator\Model\Context\CommandContext;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Context\CommandContext
 */
class CommandContextTest extends TestCase
{
    public const COMMAND_NAME = 'module:menu-item:create';
    public const COMMAND_DESCRIPTION = 'Create menu item';

    public function testGetName(): void
    {
        $this->assertEquals(self::COMMAND_NAME, self::createContext()->getName());
    }

    public function testGetDescription(): void
    {
        $this->assertEquals(self::COMMAND_DESCRIPTION, self::createContext()->getDescription());
    }

    public function testGetModule(): void
    {
        $this->assertEquals(ModuleContextTest::MODULE_NAME, self::createContext()->getModule()->getName());
    }

    public function testGetDiItemName(): void
    {
        $this->assertEquals('moduleMenuItemCreate', self::createContext()->getDiItemName());
    }

    public function testGetCommand(): void
    {
        $this->assertEquals('Vendor1\\Module1\\Command\\MenuItem\\Create', self::createContext()->getCommand()->getName());
    }

    public static function createContext(): CommandContext
    {
        return new CommandContext(
            ModuleContextTest::createContext(),
            self::COMMAND_NAME,
            self::COMMAND_DESCRIPTION
        );
    }
}
