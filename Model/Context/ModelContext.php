<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Context;

class ModelContext
{
    /**
     * @var ModuleContext
     */
    private $module;
    /**
     * @var ModuleContext
     */
    private $apiModule;
    /**
     * @var string
     */
    private $relativeClassName;
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var ModelFieldContext[]
     */
    private $fields;
    /**
     * @var ModelFieldContext
     */
    private $primaryKeyField;
    /**
     * @var array
     */
    private $classCache = [];

    public function __construct(
        ModuleContext $module,
        ModuleContext $apiModule,
        $relativeClassName,
        $tableName,
        $fields
    ) {
        $this->module = $module;
        $this->apiModule = $apiModule;
        if (!$relativeClassName) {
            throw new \RuntimeException('Relative class name is empty');
        }
        $this->relativeClassName = $relativeClassName;
        if (!$tableName) {
            throw new \RuntimeException('Table name is empty');
        }
        $this->tableName = $tableName;
        $this->fields = $fields;

        $primaryKeyField = null;
        foreach ($fields as $field) {
            if ($field instanceof ModelFieldContext) {
                if ($field->getIsPrimary()) {
                    if ($primaryKeyField) {
                        throw new \RuntimeException('There should be only one primary key');
                    }
                    $primaryKeyField = $field;
                }
            } else {
                throw new \RuntimeException('Each field should be an instance of ModelFieldContext');
            }
        }
        if (!$primaryKeyField) {
            throw new \RuntimeException('Primary key is missing');
        }
        $this->primaryKeyField = $primaryKeyField;
    }

    public function getRelativeClassName(): string
    {
        return $this->relativeClassName;
    }

    public function getClassDescription(): string
    {
        return strtolower(str_replace(['\\', '/'], '', preg_replace('/(?<!^)[A-Z]/', ' $0', $this->getRelativeClassName())));
    }

    public function getVariableName(): string
    {
        return lcfirst(str_replace(['\\', '/'], '', $this->getRelativeClassName()));
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getTableDescription(): string
    {
        return ucwords(strtr($this->getTableName(), [
            '_entity' => '',
            '_' => ' ',
        ])) . ' Table';
    }

    public function getPrimaryKey(): ModelFieldContext
    {
        return $this->primaryKeyField;
    }

    /**
     * @return ModelFieldContext[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getModule(): ModuleContext
    {
        return $this->module;
    }

    public function getApiModule(): ModuleContext
    {
        return $this->apiModule;
    }

    public function getEventPrefixName(): string
    {
        [, $module] = explode('_', $this->module->getName());

        return strtolower($module . '_' . str_replace(['\\', '/'], '', preg_replace('/(?<!^)[A-Z]/', '_$0', $this->relativeClassName)));
    }

    public function getEventObjectName(): string
    {
        return strtolower(str_replace(['\\', '/'], '', preg_replace('/(?<!^)[A-Z]/', '_$0', $this->relativeClassName)));
    }

    public function getAclResourceName(): string
    {
        return $this->module->getName() . '::' . $this->getEventObjectName();
    }

    public function getModelInterface(): ClassContext
    {
        return $this->getClassContext($this->apiModule, 'Api\\Data\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . 'Interface');
    }

    public function getModel(): ClassContext
    {
        return $this->getClassContext($this->module, 'Model\\'
            . str_replace('/', '\\', $this->getRelativeClassName()));
    }

    public function getSearchResultsInterface(): ClassContext
    {
        return $this->getClassContext($this->apiModule, 'Api\\Data\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . 'SearchResultsInterface');
    }

    public function getSearchResults(): ClassContext
    {
        return $this->getClassContext($this->module, 'Model\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . 'SearchResults');
    }

    public function getRepositoryInterface(): ClassContext
    {
        return $this->getClassContext($this->apiModule, 'Api\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . 'RepositoryInterface');
    }

    public function getRepository(): ClassContext
    {
        return $this->getClassContext($this->module, 'Model\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . 'Repository');
    }

    public function getResourceModel(): ClassContext
    {
        return $this->getClassContext($this->module, 'Model\\ResourceModel\\'
            . str_replace('/', '\\', $this->getRelativeClassName()));
    }

    public function getCollection(): ClassContext
    {
        return $this->getClassContext($this->module, 'Model\\ResourceModel\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . '\\Collection');
    }

    public function getFixtureAbsolutePath($testType, $name): string
    {
        return $this->module->getPath() . '/Test/' . $testType . '/_files/' . $name . '.php';
    }

    public function getFixtureRelativePath($testType, $name): string
    {
        return '../../../../app/code/' . str_replace('_', '/', $this->module->getName()) . '/Test/' . $testType . '/_files/' . $name . '.php';
    }

    private function getClassContext(ModuleContext $module, string $relativeClassName): ClassContext
    {
        $className = str_replace('_', '\\', $module->getName()) . '\\' . $relativeClassName;
        if (!isset($this->classCache[$className])) {
            $this->classCache[$className] = new ClassContext($module, $className);
        }

        return $this->classCache[$className];
    }
}
