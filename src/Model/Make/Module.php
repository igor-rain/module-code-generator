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

class Module
{
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
        ComposerJsonGenerator $composerJsonGenerator,
        RegistrationPhpGenerator $registrationPhpGenerator,
        ModuleXmlGenerator $moduleXmlGenerator
    ) {
        $this->composerJsonGenerator = $composerJsonGenerator;
        $this->registrationPhpGenerator = $registrationPhpGenerator;
        $this->moduleXmlGenerator = $moduleXmlGenerator;
    }

    public function make(ModuleContext $context): void
    {
        $this->composerJsonGenerator->generate($context->getPath() . '/composer.json', $context);
        $this->registrationPhpGenerator->generate($context->getPath() . '/registration.php', $context);
        $this->moduleXmlGenerator->generate($context->getPath() . '/etc/module.xml', $context);
    }
}
