<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Command\Make;

use IgorRain\CodeGenerator\Model\Command\QuestionFactory;
use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Make\Module as MakeModule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Module extends Command
{
    public const NAME = 'dev:make:module';
    /**
     * @var ModuleContextBuilder
     */
    private $moduleContextBuilder;
    /**
     * @var MakeModule
     */
    private $makeModule;
    /**
     * @var QuestionFactory
     */
    private $questionFactory;

    public function __construct(
        ModuleContextBuilder $moduleContextBuilder,
        MakeModule $makeModule,
        QuestionFactory $questionFactory
    ) {
        parent::__construct();
        $this->moduleContextBuilder = $moduleContextBuilder;
        $this->makeModule = $makeModule;
        $this->questionFactory = $questionFactory;
    }

    protected function configure(): void
    {
        $this->setName(static::NAME)
            ->setDescription('Generate module');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $moduleNameQuestion = $this->questionFactory->createNewModuleNameQuestion($this->moduleContextBuilder);
        $helper->ask($input, $output, $moduleNameQuestion);
        $module = $this->moduleContextBuilder->build();
        $this->makeModule->make($module);
    }
}
