<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Context;

class ModelFieldContext
{
    /**
     * @var null|string
     */
    private $name;
    /**
     * @var bool
     */
    private $isPrimary = false;

    public function __construct($name)
    {
        if (!$name) {
            throw new \RuntimeException('Field name is empty');
        }
        if (!preg_match('!^[a-z0-9_]*$!', $name)) {
            throw new \RuntimeException('Invalid field name ' . $name);
        }
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIsPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
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
}
