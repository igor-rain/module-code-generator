<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Make;

use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use IgorRain\CodeGenerator\Model\Generator\ComposerJsonGenerator;
use IgorRain\CodeGenerator\Model\Generator\Etc\ModuleXmlGenerator;
use IgorRain\CodeGenerator\Model\Generator\RegistrationPhpGenerator;
use IgorRain\CodeGenerator\Model\Locator;

class Module
{
    /**
     * @var Locator
     */
    private $locator;
    /**
     * @var ComposerJsonGenerator
     */
    private $composerJsonGenerator;
    /**
     * @var RegistrationPhpGenerator
     */
    private $registrationPhpGenerator;
    /**
     * @var ModuleXmlGenerator
     */
    private $moduleXmlGenerator;

    public function __construct(
        Locator $locator,
        ComposerJsonGenerator $composerJsonGenerator,
        RegistrationPhpGenerator $registrationPhpGenerator,
        ModuleXmlGenerator $moduleXmlGenerator
    ) {
        $this->locator = $locator;
        $this->composerJsonGenerator = $composerJsonGenerator;
        $this->registrationPhpGenerator = $registrationPhpGenerator;
        $this->moduleXmlGenerator = $moduleXmlGenerator;
    }

    public function make($moduleName): void
    {
        $contextModule = $this->createModuleContext($moduleName);
        $this->createModule($contextModule);
    }

    public function makeWithApi($moduleName): void
    {
        $contextApi = $this->createModuleContext($moduleName . 'Api');
        $this->createModule($contextApi);

        $contextModule = $this->createModuleContext($moduleName);
        $contextModule->setDependencies([$contextApi]);
        $this->createModule($contextModule);
    }

    protected function createModuleContext($moduleName): ModuleContext
    {
        $modulePath = $this->locator->getNewModulePath($moduleName);

        return new ModuleContext($moduleName, $modulePath);
    }

    protected function createModule(ModuleContext $context): void
    {
        $this->composerJsonGenerator->generate($context->getPath() . '/composer.json', $context);
        $this->registrationPhpGenerator->generate($context->getPath() . '/registration.php', $context);
        $this->moduleXmlGenerator->generate($context->getPath() . '/etc/module.xml', $context);
    }
}
