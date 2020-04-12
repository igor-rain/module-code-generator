<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Context;

class ModuleContext
{
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
    private $version = '0.0.1';
    /**
     * @var ModuleContext[]
     */
    private $dependencies = [];

    public function __construct($name, $path)
    {
        if (!$name) {
            throw new \RuntimeException('Module name is empty');
        }
        if (!preg_match('!^[A-Z0-9][A-Za-z0-9]*_[A-Z0-9][A-Za-z0-9]*$!', $name)) {
            throw new \RuntimeException('Invalid module name ' . $name);
        }
        $this->name = $name;

        if (!$path) {
            throw new \RuntimeException('Module path is empty');
        }
        $this->path = $path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return ModuleContext[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @param ModuleContext[] $dependencies
     */
    public function setDependencies(array $dependencies): void
    {
        $this->dependencies = $dependencies;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getComposerPackage(): string
    {
        return strtolower(str_replace('_', '/module', preg_replace('/(?<!^)[A-Z]/', '-$0', $this->name)));
    }

    public function getPsr4Prefix(): string
    {
        return str_replace('_', '\\', $this->name) . '\\';
    }

    public function getDescription(): string
    {
        return 'Magento module responsible for ' . str_replace('_', '', preg_replace('/(?<!^)[A-Z]/', ' $0', $this->name));
    }
}
