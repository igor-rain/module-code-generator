<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Context\Builder;

use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use IgorRain\CodeGenerator\Model\Locator;

class ModuleContextBuilder
{
    public const DEFAULT_VERSION = '0.0.1';
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $version;
    /**
     * @var ModuleContext[]
     */
    private $dependencies;
    /**
     * @var Locator
     */
    private $locator;

    public function __construct(Locator $locator)
    {
        $this->locator = $locator;
        $this->clear();
    }

    public function getName(): string
    {
        if (!isset($this->name)) {
            throw new \RuntimeException('Module name is not set');
        }
        return $this->name;
    }

    public function setName(string $name): self
    {
        if (!$name) {
            throw new \RuntimeException('Module name is empty');
        }
        if (!preg_match('!^[A-Z0-9][A-Za-z0-9]*_[A-Z0-9][A-Za-z0-9]*$!', $name)) {
            throw new \RuntimeException('Invalid module name ' . $name);
        }

        $this->name = $name;
        return $this;
    }

    public function getPath(): string
    {
        if (!isset($this->path)) {
            throw new \RuntimeException('Module path is not set');
        }
        return $this->path;
    }

    public function setPath(string $path): self
    {
        if (!$path) {
            throw new \RuntimeException('Module path is empty');
        }
        $this->path = $path;
        return $this;
    }

    public function setPathAsNew(): self
    {
        $path = $this->locator->getNewModulePath($this->getName());
        if (!$path) {
            throw new \RuntimeException('New module path is empty');
        }

        $this->path = $path;
        return $this;
    }

    public function setPathAsExisting(): self
    {
        $path = $this->locator->getExistingModulePath($this->getName());
        if (!$path) {
            throw new \RuntimeException('Module ' . $this->name . ' was\'t found');
        }

        $this->path = $path;
        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function addDependency(ModuleContext $context): self
    {
        $this->dependencies[] = $context;
        return $this;
    }

    public function clear(): self
    {
        $this->name = null;
        $this->path = null;
        $this->version = self::DEFAULT_VERSION;
        $this->dependencies = [];
        return $this;
    }

    public function build(): ModuleContext
    {
        $context = new ModuleContext(
            $this->getName(),
            $this->getPath(),
            $this->getVersion(),
            $this->dependencies
        );

        $this->clear();

        return $context;
    }
}
