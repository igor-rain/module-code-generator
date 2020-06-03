<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Command\Make;

use IgorRain\CodeGenerator\Model\Command\QuestionFactory;
use IgorRain\CodeGenerator\Model\Context\Builder\ModelContextBuilder;
use IgorRain\CodeGenerator\Model\Context\Builder\ModelFieldContextBuilder;
use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Context\ModelFieldContext;
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
    /**
     * @var QuestionFactory
     */
    private $questionFactory;

    public function __construct(
        ModelContextBuilder $modelContextBuilder,
        ModelFieldContextBuilder $modelFieldContextBuilder,
        ModuleContextBuilder $moduleContextBuilder,
        MakeModel $makeModel,
        QuestionFactory $questionFactory
    ) {
        parent::__construct();
        $this->modelContextBuilder = $modelContextBuilder;
        $this->modelFieldContextBuilder = $modelFieldContextBuilder;
        $this->moduleContextBuilder = $moduleContextBuilder;
        $this->makeModel = $makeModel;
        $this->questionFactory = $questionFactory;
    }

    protected function configure(): void
    {
        $this->setName(static::NAME)
            ->setDescription('Generate model');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->askModule($input, $output);
        $this->askModelName($input, $output);
        $this->askTableName($input, $output);

        $this->askField($input, $output, 'Primary key', true, false);

        $this->askField($input, $output, 'Identifier', false, true);

        for ($fieldIndex = 1;; ++$fieldIndex) {
            if (!$this->askField($input, $output, 'Field #' . $fieldIndex, false, false)) {
                break;
            }
        }

        $context = $this->modelContextBuilder->build();
        $this->makeModel->make($context);
    }

    protected function askModule(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $moduleNameQuestion = $this->questionFactory->createExistingModuleNameQuestion($this->moduleContextBuilder);
        $moduleName = $helper->ask($input, $output, $moduleNameQuestion);
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

        $modelNameQuestion = $this->questionFactory->getModelNameQuestion($this->modelContextBuilder);
        $helper->ask($input, $output, $modelNameQuestion);
    }

    protected function askTableName(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        $tableNameQuestion = $this->questionFactory->getTableNameQuestion($this->modelContextBuilder);
        $helper->ask($input, $output, $tableNameQuestion);
    }

    protected function askField(
        InputInterface $input,
        OutputInterface $output,
        string $questionPrefix,
        bool $isPrimary,
        bool $isIdentifier
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
        $fieldTypeQuestion->setAutocompleterValues(ModelFieldContext::TYPES);
        $fieldTypeQuestion->setValidator(function ($value) {
            $this->modelFieldContextBuilder->setType((string)$value);
        });
        $helper->ask($input, $output, $fieldTypeQuestion);

        $this->modelFieldContextBuilder->setIsPrimary($isPrimary);
        $this->modelFieldContextBuilder->setIsIdentifier($isIdentifier);

        $field = $this->modelFieldContextBuilder->build();
        $this->modelContextBuilder->addField($field);

        return true;
    }
}
