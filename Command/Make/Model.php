<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Command\Make;

use IgorRain\CodeGenerator\Model\Context\Builder\ModelContextBuilder;
use IgorRain\CodeGenerator\Model\Context\Builder\ModelFieldContextBuilder;
use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Make\Model as MakeModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Model extends Command
{
    public const NAME = 'dev:make:model';
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

    public function __construct(
        ModelContextBuilder $modelContextBuilder,
        ModelFieldContextBuilder $modelFieldContextBuilder,
        ModuleContextBuilder $moduleContextBuilder,
        MakeModel $makeModel
    ) {
        $this->modelContextBuilder = $modelContextBuilder;
        $this->modelFieldContextBuilder = $modelFieldContextBuilder;
        $this->moduleContextBuilder = $moduleContextBuilder;
        $this->makeModel = $makeModel;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(static::NAME)
            ->setDescription(
                'Generate model'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $this->askModule($input, $output);
        $this->askModelName($input, $output);
        $this->askTableName($input, $output);

        $this->askField($input, $output, 'Primary key', true);

        for ($fieldIndex = 1;; ++$fieldIndex) {
            if (!$this->askField($input, $output, 'Field #' . $fieldIndex, false)) {
                break;
            }
        }

        $context = $this->modelContextBuilder->build();
        $this->makeModel->make($context);
    }

    protected function askModule(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $moduleNameQuestion = new Question('Module name (e.g. Vendor_Module): ');
        $moduleNameQuestion->setValidator(function ($value) {
            $this->moduleContextBuilder->setName((string)$value);
            $this->moduleContextBuilder->setPathAsExisting();
        });
        $helper->ask($input, $output, $moduleNameQuestion);

        $moduleName = $this->moduleContextBuilder->getName();
        $module = $this->moduleContextBuilder->build();

        $this->modelContextBuilder->setModule($module);

        try {
            $apiModule = $this->moduleContextBuilder
                ->setName($moduleName . 'Api')
                ->setPathAsExisting()
                ->build();
            $this->modelContextBuilder->setApiModule($apiModule);
        } catch (\RuntimeException $exception) {
        }

        try {
            $graphQlModule = $this->moduleContextBuilder
                ->setName($moduleName . 'GraphQl')
                ->setPathAsExisting()
                ->build();
            $this->modelContextBuilder->setGraphQlModule($graphQlModule);
        } catch (\RuntimeException $exception) {
        }
    }

    protected function askModelName(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $modelNameQuestion = new Question('Model name (e.g. Product): ');
        $modelNameQuestion->setValidator(function ($value) {
            $this->modelContextBuilder->setName((string)$value);
        });
        $helper->ask($input, $output, $modelNameQuestion);
    }

    protected function askTableName(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $tableNameQuestion = new Question('Table name (e.g. catalog_product_entity): ');
        $tableNameQuestion->setValidator(function ($value) {
            $this->modelContextBuilder->setTableName((string)$value);
        });
        $helper->ask($input, $output, $tableNameQuestion);
    }

    protected function askField(
        InputInterface $input,
        OutputInterface $output,
        string $questionPrefix,
        bool $isPrimary
    ): bool {
        $helper = $this->getHelper('question');

        $fieldNameQuestion = new Question($questionPrefix . ' name: ');
        $fieldNameQuestion->setValidator(function ($value) {
            if ($value) {
                $this->modelFieldContextBuilder->setName((string)$value);
            }
            return $value;
        });
        $fieldName = $helper->ask($input, $output, $fieldNameQuestion);
        if (!$fieldName) {
            return false;
        }

        $fieldTypeQuestion = new Question($questionPrefix . ' type: ');
        $fieldTypeQuestion->setValidator(function ($value) {
            $this->modelFieldContextBuilder->setType((string)$value);
        });
        $helper->ask($input, $output, $fieldTypeQuestion);

        $this->modelFieldContextBuilder->setIsPrimary($isPrimary);

        $field = $this->modelFieldContextBuilder->build();
        $this->modelContextBuilder->addField($field);

        return true;
    }
}
