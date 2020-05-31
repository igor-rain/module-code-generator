<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Command;

use IgorRain\CodeGenerator\Model\Context\Builder\ModelContextBuilder;
use IgorRain\CodeGenerator\Model\Context\Builder\ModuleContextBuilder;
use IgorRain\CodeGenerator\Model\Locator;
use Symfony\Component\Console\Question\Question;

class QuestionFactory
{
    /**
     * @var Locator
     */
    private $locator;

    public function __construct(
        Locator $locator
    ) {
        $this->locator = $locator;
    }

    public function createNewModuleNameQuestion(ModuleContextBuilder $moduleContextBuilder): Question
    {
        $moduleNameQuestion = new Question('Module name (e.g. Vendor_Module): ');
        $moduleNameQuestion->setValidator(static function ($value) use ($moduleContextBuilder) {
            $moduleContextBuilder->setName((string)$value);
            $moduleContextBuilder->setPathAsNew();
            return $value;
        });
        return $moduleNameQuestion;
    }

    public function createExistingModuleNameQuestion(ModuleContextBuilder $moduleContextBuilder): Question
    {
        $moduleNameQuestion = new Question('Module name (e.g. Vendor_Module): ');
        $moduleNameQuestion->setAutocompleterValues($this->locator->getExistingModuleNames());
        $moduleNameQuestion->setValidator(static function ($value) use ($moduleContextBuilder) {
            $moduleContextBuilder->setName((string)$value);
            $moduleContextBuilder->setPathAsExisting();
            return $value;
        });
        return $moduleNameQuestion;
    }

    public function getModelNameQuestion(ModelContextBuilder $modelContextBuilder): Question
    {
        $modelNameQuestion = new Question('Model name (e.g. Product): ');
        $modelNameQuestion->setValidator(static function ($value) use ($modelContextBuilder) {
            $modelContextBuilder->setName((string)$value);
        });
        return $modelNameQuestion;
    }

    public function getTableNameQuestion(ModelContextBuilder $modelContextBuilder): Question
    {
        $tableNameQuestion = new Question('Table name (e.g. catalog_product_entity): ');
        $tableNameQuestion->setValidator(static function ($value) use ($modelContextBuilder) {
            $modelContextBuilder->setTableName((string)$value);
        });
        return $tableNameQuestion;
    }
}
