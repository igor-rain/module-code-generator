<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Context;

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

    public function __construct(
        ModuleContext $module,
        $className
    ) {
        $this->module = $module;
        if (!$className) {
            throw new \RuntimeException('Class name is empty');
        }
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
        $version = 'V1';
        $preserveVersion = true;

        if (!preg_match('/^(.+?)\\\\(.+?)\\\\Service\\\\(V\d+)+(\\\\.+)Interface$/', $this->className, $matches)) {
            $apiClassPattern = "#^(.+?)\\\\(.+?)\\\\Api\\\\(.+?)(Interface)?$#";
            preg_match($apiClassPattern, $this->className, $matches);
        }

        if (!empty($matches)) {
            [, $moduleNamespace, $moduleName] = $matches;
            $moduleNamespace = ($moduleNamespace === 'Magento') ? '' : $moduleNamespace;
            if ($matches[4] === 'Interface') {
                $matches[4] = $matches[3];
            }
            $serviceNameParts = explode('\\', trim($matches[4], '\\'));
            if ($moduleName === $serviceNameParts[0]) {
                /** Avoid duplication of words in service name */
                $moduleName = '';
            }
            $parentServiceName = $moduleNamespace . $moduleName . array_shift($serviceNameParts);
            array_unshift($serviceNameParts, $parentServiceName);
            if ($preserveVersion) {
                $serviceNameParts[] = $version;
            }
        } elseif (preg_match('/^(.+?)\\\\(.+?)\\\\Api(\\\\.+)Interface$/', $this->className, $matches)) {
            [, $moduleNamespace, $moduleName] = $matches;
            $moduleNamespace = ($moduleNamespace === 'Magento') ? '' : $moduleNamespace;
            $serviceNameParts = explode('\\', trim($matches[3], '\\'));
            if ($moduleName === $serviceNameParts[0]) {
                /** Avoid duplication of words in service name */
                $moduleName = '';
            }
            $parentServiceName = $moduleNamespace . $moduleName . array_shift($serviceNameParts);
            array_unshift($serviceNameParts, $parentServiceName);
            if ($preserveVersion) {
                $serviceNameParts[] = $version;
            }
        } else {
            throw new \InvalidArgumentException(sprintf('The service interface name "%s" is invalid.', $this->className));
        }
        return lcfirst(implode('', $serviceNameParts));
    }
}
