<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Context\Builder;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\Context\ModelFieldContext;
use IgorRain\CodeGenerator\Model\Context\ModuleContext;

class ModelContextBuilder
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

    public function __construct()
    {
        $this->clear();
    }

    public function setName(string $name): self
    {
        if (!$name) {
            throw new \RuntimeException('Model name is empty');
        }
        if (!preg_match('!^[A-Z][A-Za-z0-9/\\\]+$!', $name)) {
            throw new \RuntimeException('Invalid model name ' . $name);
        }

        $this->name = $name;
        return $this;
    }

    public function setTableName(string $tableName): self
    {
        if (!$tableName) {
            throw new \RuntimeException('Table name is empty');
        }
        if (!preg_match('!^[a-z0-9_]+$!', $tableName)) {
            throw new \RuntimeException('Invalid table name ' . $tableName);
        }
        $this->tableName = $tableName;
        return $this;
    }

    public function addField(ModelFieldContext $field): self
    {
        $this->fields[] = $field;
        return $this;
    }

    public function setModule(ModuleContext $module): self
    {
        $this->module = $module;
        if ($this->apiModule === null) {
            $this->apiModule = $module;
        }
        if ($this->graphQlModule === null) {
            $this->graphQlModule = $module;
        }
        return $this;
    }

    public function setApiModule(ModuleContext $apiModule): self
    {
        $this->apiModule = $apiModule;
        return $this;
    }

    public function setGraphQlModule(ModuleContext $graphQlModule): self
    {
        $this->graphQlModule = $graphQlModule;
        return $this;
    }

    public function clear(): self
    {
        $this->module = null;
        $this->apiModule = null;
        $this->graphQlModule = null;
        $this->name = null;
        $this->tableName = null;
        $this->fields = [];
        return $this;
    }

    public function build(): ModelContext
    {
        if (!isset($this->name)) {
            throw new \RuntimeException('Model name is not set');
        }
        if (!isset($this->tableName)) {
            throw new \RuntimeException('Table name is not set');
        }
        if (!isset($this->module)) {
            throw new \RuntimeException('Module is not set');
        }

        $context = new ModelContext(
            $this->module,
            $this->apiModule,
            $this->graphQlModule,
            $this->name,
            $this->tableName,
            $this->fields
        );

        $this->clear();

        return $context;
    }
}
