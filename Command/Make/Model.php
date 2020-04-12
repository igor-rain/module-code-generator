<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Command\Make;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\Context\ModelFieldContext;
use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use IgorRain\CodeGenerator\Model\Locator;
use Magento\Framework\App\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Model extends Command
{
    public const NAME = 'dev:make:model';

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription(
                'Generate model'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $objectManager = ObjectManager::getInstance();
        /** @var Locator $locator */
        $locator = $objectManager->get(Locator::class);

        $helper = $this->getHelper('question');

        $moduleNameQuestion = new Question('Module name: ');
        $moduleName = $helper->ask($input, $output, $moduleNameQuestion);

        $modulePath = $locator->getModulePath($moduleName);
        if (!$modulePath) {
            throw new \RuntimeException('Module not found ' . $moduleName);
        }

        $moduleContext = new ModuleContext($moduleName, $modulePath);
        $moduleApiContext = $moduleContext;

        $apiModuleName = $moduleName . 'Api';
        $apiModulePath = $locator->getModulePath($apiModuleName);
        if ($apiModulePath) {
            $moduleApiContext = new ModuleContext($apiModuleName, $apiModulePath);
        }

        $classNameQuestion = new Question('Class name: ');
        $className = $helper->ask($input, $output, $classNameQuestion);

        $tableNameQuestion = new Question('Table name: ');
        $tableName = $helper->ask($input, $output, $tableNameQuestion);

        $fields = [];

        $primaryKeyDefault = 'entity_id';
        $primaryKeyQuestion = new Question('Primary key (default is ' . $primaryKeyDefault . '): ', $primaryKeyDefault);
        $primaryKey = $helper->ask($input, $output, $primaryKeyQuestion);
        $primaryKeyField = new ModelFieldContext($primaryKey);
        $primaryKeyField->setIsPrimary(true);
        $fields[] = $primaryKeyField;

        for ($fieldIndex = 1;; ++$fieldIndex) {
            $fieldQuestion = new Question('Field #' . $fieldIndex . ': ');
            $fieldName = $helper->ask($input, $output, $fieldQuestion);
            if ($fieldName) {
                $fields[] = new ModelFieldContext($fieldName);
            } else {
                break;
            }
        }

        $context = new ModelContext(
            $moduleContext,
            $moduleApiContext,
            $className,
            $tableName,
            $fields
        );

        $objectManager = ObjectManager::getInstance();
        $makeModel = $objectManager->get(\IgorRain\CodeGenerator\Model\Make\Model::class);

        $makeModel->make($context);
    }
}
