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
    private $version;
    /**
     * @var ModuleContext[]
     */
    private $dependencies;

    public function __construct(string $name, string $path, string $version, array $dependencies)
    {
        $this->name = $name;
        $this->path = $path;
        $this->version = $version;
        $this->dependencies = $dependencies;
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

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getComposerPackage(): string
    {
        return strtolower(str_replace('_', '/module', preg_replace('/(?<!^)[A-Z]+/', '-$0', $this->name)));
    }

    public function getPsr4Prefix(): string
    {
        return str_replace('_', '\\', $this->name) . '\\';
    }

    public function getDescription(): string
    {
        return 'N/A';
    }
}
