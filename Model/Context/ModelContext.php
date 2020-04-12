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

    /**
     * @return string
     */
    public function getRelativeClassName()
    {
        return $this->relativeClassName;
    }

    /**
     * @return string
     */
    public function getClassDescription()
    {
        return strtolower(str_replace(['\\', '/'], '', preg_replace('/(?<!^)[A-Z]/', ' $0', $this->getRelativeClassName())));
    }

    /**
     * @return string
     */
    public function getVariableName()
    {
        return lcfirst(str_replace(['\\', '/'], '', $this->getRelativeClassName()));
    }

    /**
     * @return null|string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return null|string
     */
    public function getTableDescription()
    {
        return ucwords(strtr($this->getTableName(), [
            '_entity' => '',
            '_' => ' ',
        ])) . ' Table';
    }

    /**
     * @return ModelFieldContext
     */
    public function getPrimaryKey()
    {
        return $this->primaryKeyField;
    }

    /**
     * @return ModelFieldContext[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return ModuleContext
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return ModuleContext
     */
    public function getApiModule()
    {
        return $this->apiModule;
    }

    public function getEventPrefixName()
    {
        [, $module] = explode('_', $this->module->getName());

        return strtolower($module . '_' . str_replace(['\\', '/'], '', preg_replace('/(?<!^)[A-Z]/', '_$0', $this->relativeClassName)));
    }

    public function getEventObjectName()
    {
        return strtolower(str_replace(['\\', '/'], '', preg_replace('/(?<!^)[A-Z]/', '_$0', $this->relativeClassName)));
    }

    /**
     * @return ClassContext
     */
    public function getModelInterface()
    {
        return $this->getClassContext($this->apiModule, 'Api\\Data\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . 'Interface');
    }

    /**
     * @return ClassContext
     */
    public function getModel()
    {
        return $this->getClassContext($this->module, 'Model\\'
            . str_replace('/', '\\', $this->getRelativeClassName()));
    }

    /**
     * @return ClassContext
     */
    public function getSearchResultsInterface()
    {
        return $this->getClassContext($this->apiModule, 'Api\\Data\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . 'SearchResultsInterface');
    }

    /**
     * @return ClassContext
     */
    public function getSearchResults()
    {
        return $this->getClassContext($this->module, 'Model\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . 'SearchResults');
    }

    /**
     * @return ClassContext
     */
    public function getRepositoryInterface()
    {
        return $this->getClassContext($this->apiModule, 'Api\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . 'RepositoryInterface');
    }

    /**
     * @return ClassContext
     */
    public function getRepository()
    {
        return $this->getClassContext($this->module, 'Model\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . 'Repository');
    }

    /**
     * @return ClassContext
     */
    public function getResourceModel()
    {
        return $this->getClassContext($this->module, 'Model\\ResourceModel\\'
            . str_replace('/', '\\', $this->getRelativeClassName()));
    }

    /**
     * @return ClassContext
     */
    public function getCollection()
    {
        return $this->getClassContext($this->module, 'Model\\ResourceModel\\'
            . str_replace('/', '\\', $this->getRelativeClassName())
            . '\\Collection');
    }

    public function getFixtureAbsolutePath($testType, $name)
    {
        return $this->module->getPath() . '/Test/' . $testType . '/_files/' . $name . '.php';
    }

    public function getFixtureRelativePath($testType, $name)
    {
        return '../../../../app/code/' . str_replace('_', '/', $this->module->getName()) . '/Test/' . $testType . '/_files/' . $name . '.php';
    }

    /**
     * @param ModuleContext $module
     * @param $relativeClassName
     *
     * @return ClassContext
     */
    private function getClassContext($module, $relativeClassName)
    {
        $className = str_replace('_', '\\', $module->getName()) . '\\' . $relativeClassName;
        if (!isset($this->classCache[$className])) {
            $this->classCache[$className] = new ClassContext($module, $className);
        }

        return $this->classCache[$className];
    }
}
