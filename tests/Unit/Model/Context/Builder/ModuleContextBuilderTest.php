<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Context\Builder;

use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Locator;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModuleContextTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder
 */
class ModuleContextBuilderTest extends TestCase
{
    /**
     * @var ModuleContextBuilder
     */
    private $builder;
    /**
     * @var MockObject|Locator
     */
    private $locator;

    public function setUp(): void
    {
        $this->locator = $this->createMock(Locator::class);
        $this->builder = new ModuleContextBuilder($this->locator);
    }

    public function testBuild(): void
    {
        $context = $this->builder
            ->setName(ModuleContextTest::MODULE_NAME)
            ->setPath(ModuleContextTest::MODULE_PATH)
            ->setVersion(ModuleContextTest::MODULE_VERSION)
            ->addDependency(ModuleContextTest::createApiContext())
            ->build();

        $dependencies = $context->getDependencies();

        $this->assertEquals(ModuleContextTest::MODULE_NAME, $context->getName());
        $this->assertEquals(ModuleContextTest::MODULE_PATH, $context->getPath());
        $this->assertEquals(ModuleContextTest::MODULE_VERSION, $context->getVersion());

        $this->assertCount(1, $dependencies);
        $this->assertEquals(ModuleContextTest::MODULE_API_NAME, $dependencies[0]->getName());
    }

    public function testBuildWithoutName(): void
    {
        $this->expectExceptionMessage('Module name is not set');
        $this->builder->build();
    }

    public function testBuildWithoutPath(): void
    {
        $this->expectExceptionMessage('Module path is not set');
        $this->builder->setName('Vendor1_Module1')
            ->build();
    }

    public function testSetNameUsingEmptyName(): void
    {
        $this->expectExceptionMessage('Module name is empty');
        $this->builder->setName('');
    }

    public function testSetNameUsingInvalidName(): void
    {
        $this->expectExceptionMessage('Invalid module name test');
        $this->builder->setName('test');
    }

    public function testSetPathUsingEmptyPath(): void
    {
        $this->expectExceptionMessage('Module path is empty');
        $this->builder->setPath('');
    }

    public function testSetPathAsNew(): void
    {
        $this->locator
            ->expects($this->once())
            ->method('getNewModulePath')
            ->with('Vendor1_Module1')
            ->willReturn('/test/aaa');
        $this->builder->setName('Vendor1_Module1');
        $this->builder->setPathAsNew();
        $this->assertEquals('/test/aaa', $this->builder->getPath());
    }

    public function testSetPathAsNewEmpty(): void
    {
        $this->expectExceptionMessage('New module path is empty');

        $this->locator
            ->expects($this->once())
            ->method('getNewModulePath')
            ->with('Vendor1_Module1')
            ->willReturn('');
        $this->builder->setName('Vendor1_Module1');
        $this->builder->setPathAsNew();
    }

    public function testSetPathAsExisting(): void
    {
        $this->locator
            ->expects($this->once())
            ->method('getExistingModulePath')
            ->with('Vendor1_Module1')
            ->willReturn('/test/aaa');
        $this->builder->setName('Vendor1_Module1');
        $this->builder->setPathAsExisting();
        $this->assertEquals('/test/aaa', $this->builder->getPath());
    }

    public function testSetPathAsExistingEmpty(): void
    {
        $this->expectExceptionMessage('Module Vendor1_Module1 was\'t found');

        $this->locator
            ->expects($this->once())
            ->method('getExistingModulePath')
            ->with('Vendor1_Module1')
            ->willReturn('');
        $this->builder->setName('Vendor1_Module1');
        $this->builder->setPathAsExisting();
    }

    public function testClear(): void
    {
        $this->expectExceptionMessage('Module name is not set');
        $this->builder
            ->setName(ModuleContextTest::MODULE_NAME)
            ->setPath(ModuleContextTest::MODULE_PATH)
            ->setVersion(ModuleContextTest::MODULE_VERSION)
            ->addDependency(ModuleContextTest::createApiContext())
            ->clear()
            ->build();
    }
}
