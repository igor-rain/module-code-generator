<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Command\Make;

use IgorRain\CodeGenerator\Command\Make\Model;
use IgorRain\CodeGenerator\Model\Command\QuestionFactory;
use IgorRain\CodeGenerator\Model\Context\Builder\ModelContextBuilder;
use IgorRain\CodeGenerator\Model\Context\Builder\ModelFieldContextBuilder;
use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\Locator;
use IgorRain\CodeGenerator\Model\Make\Model as MakeModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \IgorRain\CodeGenerator\Command\Make\Model
 */
class ModelTest extends TestCase
{
    /**
     * @var Locator|MockObject
     */
    private $locator;
    /**
     * @var MakeModel|MockObject
     */
    private $makeModel;
    /**
     * @var Model
     */
    private $moduleCommand;

    public function setUp(): void
    {
        $this->locator = $this->createMock(Locator::class);
        $this->makeModel = $this->createMock(MakeModel::class);
        $this->moduleCommand = new Model(
            new ModelContextBuilder(),
            new ModelFieldContextBuilder(),
            new ModuleContextBuilder($this->locator),
            $this->makeModel,
            new QuestionFactory($this->locator)
        );
        $application = new Application();
        $application->add($this->moduleCommand);
    }

    public function testName(): void
    {
        $this->assertEquals('dev:make:model', $this->moduleCommand->getName());
    }

    public function testRun(): void
    {
        $commandTester = new CommandTester($this->moduleCommand);
        $commandTester->setInputs($this->prepareInput());

        $this->locator
            ->method('getExistingModulePath')
            ->willReturnCallback(static function ($moduleName) {
                return '/tmp/magento/app/code/' . str_replace('_', '/', $moduleName);
            });

        $this->makeModel
            ->expects($this->once())
            ->method('make')
            ->willReturnCallback(function (ModelContext $model) {
                $this->assertEquals('Vendor1_Module1', $model->getModule()->getName());
                $this->assertEquals('Vendor1_Module1Api', $model->getApiModule()->getName());
                $this->assertEquals('Vendor1_Module1GraphQl', $model->getGraphQlModule()->getName());
                $this->assertCount(3, $model->getFields());
                $this->assertEquals('Menu/Item', $model->getName());
                $this->assertEquals('catalog_menu_item', $model->getTableName());
                $this->assertEquals('entity_id', $model->getPrimaryField()->getName());
            });

        $commandTester->execute([]);
    }

    public function testRunWithMissingApiAndGraphQlModules(): void
    {
        $commandTester = new CommandTester($this->moduleCommand);
        $commandTester->setInputs($this->prepareInput());

        $this->locator
            ->method('getExistingModulePath')
            ->willReturnCallback(static function ($moduleName) {
                if ($moduleName === 'Vendor1_Module1') {
                    return '/tmp/magento/app/code/' . str_replace('_', '/', $moduleName);
                }
                return null;
            });

        $this->makeModel
            ->expects($this->once())
            ->method('make')
            ->willReturnCallback(function (ModelContext $model) {
                $this->assertEquals('Vendor1_Module1', $model->getModule()->getName());
                $this->assertEquals('Vendor1_Module1', $model->getApiModule()->getName());
                $this->assertEquals('Vendor1_Module1', $model->getGraphQlModule()->getName());
            });

        $commandTester->execute([]);
    }

    protected function prepareInput(): array
    {
        return [
            'Vendor1_Module1',
            'Menu/Item',
            'catalog_menu_item',
            'entity_id',
            'int',
            'name',
            'string',
            'description',
            'text',
            ''
        ];
    }
}
