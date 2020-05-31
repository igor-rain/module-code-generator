<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator;

use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;

class RegistrationPhpGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generate($fileName, ModuleContext $context): void
    {
        /** @var TextSource $source */
        $source = $this->sourceFactory->create($fileName, 'text');
        $source->setContent($this->prepareData($context));
        $source->save();
    }

    protected function prepareData(ModuleContext $context): string
    {
        return '<?php

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    \'' . $context->getName() . '\',
    __DIR__
);
';
    }
}
