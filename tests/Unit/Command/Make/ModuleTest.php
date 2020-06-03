<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Command\Make;

use IgorRain\CodeGenerator\Command\Make\Module;
use IgorRain\CodeGenerator\Model\Command\QuestionFactory;
use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use IgorRain\CodeGenerator\Model\Make\Module as MakeModule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \IgorRain\CodeGenerator\Command\Make\Module
 */
class ModuleTest extends TestCase
{
    /**
     * @var ModuleContextBuilder|MockObject
     */
    private $moduleContextBuilder;
    /**
     * @var MakeModule|MockObject
     */
    private $makeModule;
    /**
     * @var QuestionFactory|MockObject
     */
    private $questionFactory;
    /**
     * @var Module
     */
    private $moduleCommand;

    public function setUp(): void
    {
        $this->moduleContextBuilder = $this->createMock(ModuleContextBuilder::class);
        $this->makeModule = $this->createMock(MakeModule::class);
        $this->questionFactory = $this->createMock(QuestionFactory::class);
        $this->moduleCommand = new Module(
            $this->moduleContextBuilder,
            $this->makeModule,
            $this->questionFactory
        );
        $application = new Application();
        $application->add($this->moduleCommand);
    }

    public function testName(): void
    {
        $this->assertEquals('dev:make:module', $this->moduleCommand->getName());
    }

    public function testRun(): void
    {
        $commandTester = new CommandTester($this->moduleCommand);

        $this->questionFactory
            ->expects($this->once())
            ->method('createNewModuleNameQuestion')
            ->willReturn(new Question('Q'));

        $commandTester->setInputs(['Vendor1_Module1']);

        $moduleContext = $this->createMock(ModuleContext::class);

        $this->moduleContextBuilder
            ->expects($this->once())
            ->method('build')
            ->willReturn($moduleContext);

        $this->makeModule
            ->expects($this->once())
            ->method('make')
            ->with($moduleContext);

        $commandTester->execute([]);
    }
}
