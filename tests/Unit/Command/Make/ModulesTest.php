<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model;

use IgorRain\CodeGenerator\Command\Make\Modules;
use IgorRain\CodeGenerator\Model\Command\QuestionFactory;
use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Make\Modules as MakeModules;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \IgorRain\CodeGenerator\Command\Make\Modules
 */
class ModulesTest extends TestCase
{
    /**
     * @var MakeModules|MockObject
     */
    private $makeModules;
    /**
     * @var QuestionFactory|MockObject
     */
    private $questionFactory;
    /**
     * @var Modules
     */
    private $modulesCommand;

    public function setUp(): void
    {
        $moduleContextBuilder = $this->createMock(ModuleContextBuilder::class);
        $this->makeModules = $this->createMock(MakeModules::class);
        $this->questionFactory = $this->createMock(QuestionFactory::class);
        $this->modulesCommand = new Modules(
            $moduleContextBuilder,
            $this->makeModules,
            $this->questionFactory
        );
        $application = new Application();
        $application->add($this->modulesCommand);
    }

    public function testName(): void
    {
        $this->assertEquals('dev:make:modules', $this->modulesCommand->getName());
    }

    public function testRun(): void
    {
        $commandTester = new CommandTester($this->modulesCommand);

        $this->questionFactory
            ->expects($this->once())
            ->method('createNewModuleNameQuestion')
            ->willReturn(new Question('Q'));

        $commandTester->setInputs(['Vendor1_Module1']);

        $this->makeModules
            ->expects($this->once())
            ->method('make')
            ->with('Vendor1_Module1');

        $commandTester->execute([]);
    }
}
