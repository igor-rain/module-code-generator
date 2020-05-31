<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Command\Make;

use IgorRain\CodeGenerator\Model\Command\QuestionFactory;
use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Make\Modules as MakeModules;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Modules extends Command
{
    public const NAME = 'dev:make:modules';
    /**
     * @var ModuleContextBuilder
     */
    private $moduleContextBuilder;
    /**
     * @var MakeModules
     */
    private $makeModules;
    /**
     * @var QuestionFactory
     */
    private $questionFactory;

    public function __construct(
        ModuleContextBuilder $moduleContextBuilder,
        MakeModules $makeModules,
        QuestionFactory $questionFactory
    ) {
        parent::__construct();
        $this->moduleContextBuilder = $moduleContextBuilder;
        $this->makeModules = $makeModules;
        $this->questionFactory = $questionFactory;
    }

    protected function configure(): void
    {
        $this->setName(static::NAME)
            ->setDescription('Generate modules');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $moduleNameQuestion = $this->questionFactory->createNewModuleNameQuestion($this->moduleContextBuilder);
        $moduleName = $helper->ask($input, $output, $moduleNameQuestion);
        $this->moduleContextBuilder->clear();

        $this->makeModules->make($moduleName);
    }
}
