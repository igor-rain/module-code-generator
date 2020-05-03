<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Command\Make;

use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use IgorRain\CodeGenerator\Model\Make\Module as MakeModule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

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

    public function __construct(
        ModuleContextBuilder $moduleContextBuilder,
        MakeModule $makeModule
    ) {
        $this->moduleContextBuilder = $moduleContextBuilder;
        $this->makeModule = $makeModule;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(static::NAME)
            ->setDescription(
                'Generate module'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $moduleNameQuestion = new Question('Module name (e.g. Vendor_Module): ');
        $moduleNameQuestion->setValidator(function ($value) {
            $this->moduleContextBuilder->setName((string)$value);
        });
        $helper->ask($input, $output, $moduleNameQuestion);

        $moduleName = $this->moduleContextBuilder->getName();
        $this->moduleContextBuilder->clear();

        $apiModule = $this->createApiModuleContext($input, $output, $moduleName . 'Api');
        $module = $this->createModuleContext($moduleName, $apiModule);
        $graphQlModule = $this->createGraphQlModuleContext($input, $output, $moduleName . 'GraphQl', $module, $apiModule);

        if ($apiModule) {
            $this->makeModule->make($apiModule);
        }
        $this->makeModule->make($module);
        if ($graphQlModule) {
            $this->makeModule->make($graphQlModule);
        }
    }

    protected function createApiModuleContext(
        InputInterface $input,
        OutputInterface $output,
        string $apiModuleName
    ): ?ModuleContext {
        $helper = $this->getHelper('question');

        $apiQuestion = new ConfirmationQuestion('Create separated module for API? ', false);
        $createApiModule = $helper->ask($input, $output, $apiQuestion);

        if ($createApiModule) {
            return $this->moduleContextBuilder
                ->setName($apiModuleName)
                ->setPathAsNew()
                ->build();
        }
        return null;
    }

    protected function createModuleContext(string $moduleName, ?ModuleContext $apiModule): ModuleContext
    {
        $this->moduleContextBuilder
            ->setName($moduleName)
            ->setPathAsNew();
        if ($apiModule) {
            $this->moduleContextBuilder->addDependency($apiModule);
        }
        return $this->moduleContextBuilder->build();
    }

    protected function createGraphQlModuleContext(
        InputInterface $input,
        OutputInterface $output,
        string $graphQlModuleName,
        ModuleContext $module,
        ?ModuleContext $apiModule
    ): ?ModuleContext {
        $helper = $this->getHelper('question');

        $graphQlQuestion = new ConfirmationQuestion('Create separated module for GraphQl? ', false);
        $createGraphQlModule = $helper->ask($input, $output, $graphQlQuestion);

        if ($createGraphQlModule) {
            $this->moduleContextBuilder
                ->setName($graphQlModuleName)
                ->setPathAsNew();
            if ($apiModule) {
                $this->moduleContextBuilder->addDependency($apiModule);
            } else {
                $this->moduleContextBuilder->addDependency($module);
            }
            return $this->moduleContextBuilder->build();
        }
        return null;
    }
}
