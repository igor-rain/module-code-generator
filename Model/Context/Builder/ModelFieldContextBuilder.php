<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Context\Builder;

use IgorRain\CodeGenerator\Model\Context\ModelFieldContext;

class ModelFieldContextBuilder
{
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

    public function __construct()
    {
        $this->clear();
    }

    public function getName(): string
    {
        if (!isset($this->name)) {
            throw new \RuntimeException('Field name is not set');
        }
        return $this->name;
    }

    public function setName(string $name): self
    {
        if (!$name) {
            throw new \RuntimeException('Field name is empty');
        }
        if (!preg_match('!^[a-z0-9_]*$!', $name)) {
            throw new \RuntimeException('Invalid field name ' . $name);
        }

        $this->name = $name;
        return $this;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, ModelFieldContext::TYPES, true)) {
            throw new \RuntimeException('Unknown field type ' . $type);
        }
        $this->type = $type;
        return $this;
    }

    public function setIsPrimary(bool $isPrimary): self
    {
        $this->isPrimary = $isPrimary;
        return $this;
    }

    public function clear(): self
    {
        $this->name = null;
        $this->type = 'string';
        $this->isPrimary = false;
        return $this;
    }

    public function build(): ModelFieldContext
    {
        $context = new ModelFieldContext(
            $this->getName(),
            $this->type,
            $this->isPrimary
        );

        $this->clear();

        return $context;
    }
}
