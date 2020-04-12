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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return ModuleContext[]
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @param ModuleContext[] $dependencies
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getComposerPackage()
    {
        return strtolower(str_replace('_', '/module', preg_replace('/(?<!^)[A-Z]/', '-$0', $this->name)));
    }

    /**
     * @return string
     */
    public function getPsr4Prefix()
    {
        return str_replace('_', '\\', $this->name) . '\\';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Magento module responsible for ' . str_replace('_', '', preg_replace('/(?<!^)[A-Z]/', ' $0', $this->name));
    }
}
