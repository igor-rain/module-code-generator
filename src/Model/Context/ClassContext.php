<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Context;

use Magento\Framework\App\ObjectManager;
use Magento\Webapi\Model\ServiceMetadata;

class ClassContext
{
    /**
     * @var ModuleContext
     */
    private $module;
    /**
     * @var string
     */
    private $className;
    /**
     * @var array
     */
    private static $classCache = [];

    public function __construct(ModuleContext $module, string $className)
    {
        $this->module = $module;
        $this->className = $className;
    }

    public function getName(): string
    {
        return $this->className;
    }

    public function getShortName(): string
    {
        $parts = explode('\\', $this->className);

        return array_pop($parts);
    }

    public function getNamespace(): string
    {
        $parts = explode('\\', $this->className);
        array_pop($parts);

        return implode('\\', $parts);
    }

    public function getAbsoluteFilePath(): string
    {
        $parts = explode('\\', $this->className);
        $parts = array_slice($parts, 2);

        return $this->module->getPath() . '/' . implode('/', $parts) . '.php';
    }

    public function getUnitTest(): ClassContext
    {
        $parts = explode('\\', $this->className);
        $moduleParts = array_slice($parts, 0, 2);
        $modelParts = array_slice($parts, 2);
        $unitTestClassName = implode('\\', $moduleParts) . '\\Test\\Unit\\' . implode('\\', $modelParts) . 'Test';

        return new ClassContext(
            $this->module,
            $unitTestClassName
        );
    }

    public function getIntegrationTest(): ClassContext
    {
        $parts = explode('\\', $this->className);
        $moduleParts = array_slice($parts, 0, 2);
        $modelParts = array_slice($parts, 2);
        $integrationTestClassName = implode('\\', $moduleParts) . '\\Test\\Integration\\' . implode('\\', $modelParts) . 'Test';

        return new ClassContext(
            $this->module,
            $integrationTestClassName
        );
    }

    public function getApiFunctionalTest(): ClassContext
    {
        $parts = explode('\\', $this->className);
        $moduleParts = array_slice($parts, 0, 2);
        $modelParts = array_slice($parts, 2);
        $integrationTestClassName = implode('\\', $moduleParts) . '\\Test\\Api\\' . implode('\\', $modelParts) . 'Test';

        return new ClassContext(
            $this->module,
            $integrationTestClassName
        );
    }

    public function getMagentoServiceName(): string
    {
        $objectManager = ObjectManager::getInstance();
        /** @var ServiceMetadata $serviceMetadata */
        $serviceMetadata = $objectManager->get(ServiceMetadata::class);
        return $serviceMetadata->getServiceName($this->className, 'V1', true);
    }

    public static function create(ModuleContext $module, string $relativeClassName): ClassContext
    {
        $className = str_replace('_', '\\', $module->getName()) . '\\' . $relativeClassName;
        if (!isset(self::$classCache[$className])) {
            self::$classCache[$className] = new ClassContext($module, $className);
        }

        return self::$classCache[$className];
    }
}
