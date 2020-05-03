<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Context;

class ModelFieldContext
{
    public const TYPES = [
        'string',
        'text',
        'bool',
        'int',
        'float'
    ];
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $type;
    /**
     * @var bool
     */
    private $isPrimary;

    public function __construct(string $name, string $type, bool $isPrimary)
    {
        $this->name = $name;
        $this->type = $type;
        $this->isPrimary = $isPrimary;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function getConstantName(): string
    {
        return strtoupper($this->getName());
    }

    public function getMethodName($prefix): string
    {
        return $prefix . str_replace('_', '', ucwords($this->getName(), '_'));
    }

    public function getVariableName(): string
    {
        return lcfirst(str_replace('_', '', ucwords($this->getName(), '_')));
    }

    public function getDescription(): string
    {
        return strtolower(str_replace('_', ' ', $this->getName()));
    }

    public function getDescriptionInTable(): string
    {
        return str_replace('Id', 'ID', ucwords(strtolower(str_replace('_', ' ', $this->getName()))));
    }

    public function getPhpType(): string
    {
        if ($this->type === 'text') {
            return 'string';
        }
        return $this->type;
    }

    public function getGraphQlType(): string
    {
        switch ($this->type) {
            case 'text':
                return 'String';
            case 'bool':
                return 'Boolean';
        }
        return ucfirst($this->type);
    }
}
