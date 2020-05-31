<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Command\Make;

use IgorRain\CodeGenerator\Model\Context\Builder\ModelContextBuilder;
use IgorRain\CodeGenerator\Model\Context\Builder\ModelFieldContextBuilder;
use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Context\ModelFieldContext;
use IgorRain\CodeGenerator\Model\Locator;
use IgorRain\CodeGenerator\Model\Make\Model as MakeModel;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Command extends ConsoleCommand
{
    public const NAME = 'dev:make:command';
    /**
     * @var ModelContextBuilder
     */
    private $modelContextBuilder;
    /**
     * @var ModelFieldContextBuilder
     */
    private $modelFieldContextBuilder;
    /**
     * @var ModuleContextBuilder
     */
    private $moduleContextBuilder;
    /**
     * @var MakeModel
     */
    private $makeModel;
    /**
     * @var Locator
     */
    private $locator;

    public function __construct(
        ModelContextBuilder $modelContextBuilder,
        ModelFieldContextBuilder $modelFieldContextBuilder,
        ModuleContextBuilder $moduleContextBuilder,
        MakeModel $makeModel,
        Locator $locator
    ) {
        $this->modelContextBuilder = $modelContextBuilder;
        $this->modelFieldContextBuilder = $modelFieldContextBuilder;
        $this->moduleContextBuilder = $moduleContextBuilder;
        $this->makeModel = $makeModel;
        $this->locator = $locator;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(static::NAME)
            ->setDescription(
                'Generate command'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $this->askModule($input, $output);
    }

    protected function askModule(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $moduleNameQuestion = new Question('Module name (e.g. Vendor_Module): ');
        $moduleNameQuestion->setAutocompleterValues($this->locator->getExistingModuleNames());
        $moduleNameQuestion->setValidator(function ($value) {
            $this->moduleContextBuilder->setName((string)$value);
            $this->moduleContextBuilder->setPathAsExisting();
        });
        $helper->ask($input, $output, $moduleNameQuestion);

        $moduleName = $this->moduleContextBuilder->getName();
        $module = $this->moduleContextBuilder->build();

        $this->modelContextBuilder->setModule($module);
    }
}
