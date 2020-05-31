<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Context;

class CommandContext
{
    /**
     * @var ModuleContext
     */
    private $module;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $description;

    public function __construct(
        ModuleContext $module,
        string $name,
        string $description
    ) {
        $this->module = $module;
        $this->name = $name;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getModule(): ModuleContext
    {
        return $this->module;
    }

    public function getDiItemName(): string
    {
        $diItemName = preg_replace('/[^a-z0-9]+/', ' ', $this->name);
        $diItemName = ucwords($diItemName);
        $diItemName = str_replace(' ', '', $diItemName);
        return lcfirst($diItemName);
    }

    public function getCommand(): ClassContext
    {
        $nameParts = explode(':', $this->name);
        array_shift($nameParts);

        foreach ($nameParts as $index => $namePart) {
            $namePart = preg_replace('/[^a-z0-9]+/', ' ', $namePart);
            $namePart = ucwords($namePart);
            $nameParts[$index] = str_replace(' ', '', $namePart);
        }

        $className = implode('\\', $nameParts);
        return ClassContext::create($this->module, 'Command\\'
            . $className);
    }
}
