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
     * @var ModuleContext
     */
    private $graphQlModule;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var ModelFieldContext[]
     */
    private $fields;
    /**
     * @var ModelFieldContext|null
     */
    private $primaryKey;
    /**
     * @var array
     */
    private $classCache = [];

    public function __construct(
        ModuleContext $module,
        ModuleContext $apiModule,
        ModuleContext $graphQlModule,
        string $name,
        string $tableName,
        array $fields
    ) {
        $this->module = $module;
        $this->apiModule = $apiModule;
        $this->graphQlModule = $graphQlModule;
        $this->name = $name;
        $this->tableName = $tableName;
        $this->fields = $fields;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassDescription(): string
    {
        return strtolower(str_replace(['\\', '/'], '', preg_replace('/(?<!^)[A-Z]/', ' $0', $this->name)));
    }

    public function getVariableName(): string
    {
        return lcfirst(str_replace(['\\', '/'], '', $this->name));
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
        if ($this->primaryKey === null) {
            foreach ($this->fields as $field) {
                if ($field->isPrimary()) {
                    $this->primaryKey = $field;
                    break;
                }
            }
            if (!$this->primaryKey) {
                throw new \RuntimeException('Primary key is missing');
            }
        }
        return $this->primaryKey;
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

    public function getGraphQlModule(): ModuleContext
    {
        return $this->graphQlModule;
    }

    public function getEventPrefixName(): string
    {
        [, $module] = explode('_', $this->module->getName());

        return strtolower($module . '_' . str_replace(['\\', '/'], '', preg_replace('/(?<!^)[A-Z]/', '_$0', $this->name)));
    }

    public function getEventObjectName(): string
    {
        return strtolower(str_replace(['\\', '/'], '', preg_replace('/(?<!^)[A-Z]/', '_$0', $this->name)));
    }

    public function getAclResourceName(): string
    {
        return $this->module->getName() . '::' . $this->getEventObjectName();
    }

    public function getModelInterface(): ClassContext
    {
        return $this->getClassContext($this->apiModule, 'Api\\Data\\'
            . str_replace('/', '\\', $this->name)
            . 'Interface');
    }

    public function getModel(): ClassContext
    {
        return $this->getClassContext($this->module, 'Model\\'
            . str_replace('/', '\\', $this->name));
    }

    public function getSearchResultsInterface(): ClassContext
    {
        return $this->getClassContext($this->apiModule, 'Api\\Data\\'
            . str_replace('/', '\\', $this->name)
            . 'SearchResultsInterface');
    }

    public function getSearchResults(): ClassContext
    {
        return $this->getClassContext($this->module, 'Model\\'
            . str_replace('/', '\\', $this->name)
            . 'SearchResults');
    }

    public function getRepositoryInterface(): ClassContext
    {
        return $this->getClassContext($this->apiModule, 'Api\\'
            . str_replace('/', '\\', $this->name)
            . 'RepositoryInterface');
    }

    public function getRepository(): ClassContext
    {
        return $this->getClassContext($this->module, 'Model\\'
            . str_replace('/', '\\', $this->name)
            . 'Repository');
    }

    public function getResourceModel(): ClassContext
    {
        return $this->getClassContext($this->module, 'Model\\ResourceModel\\'
            . str_replace('/', '\\', $this->name));
    }

    public function getCollection(): ClassContext
    {
        return $this->getClassContext($this->module, 'Model\\ResourceModel\\'
            . str_replace('/', '\\', $this->name)
            . '\\Collection');
    }

    public function getGraphQlModelResolver(): ClassContext
    {
        return $this->getClassContext($this->graphQlModule, 'Model\Resolver\\'
            . str_replace('/', '\\', $this->name));
    }

    public function getGraphQlModelDataProvider(): ClassContext
    {
        return $this->getClassContext($this->graphQlModule, 'Model\Resolver\DataProvider\\'
            . str_replace('/', '\\', $this->name));
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
