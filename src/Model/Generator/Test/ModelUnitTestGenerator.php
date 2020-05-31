<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Test;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;

class ModelUnitTestGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generate(string $fileName, ModelContext $context): void
    {
        /** @var TextSource $source */
        $source = $this->sourceFactory->create($fileName, 'text');
        $source->setContent($this->prepareData($context));
        $source->save();
    }

    protected function prepareData(ModelContext $context): string
    {
        return strtr($this->getTemplate(), [
            '{namespace}' => $context->getModel()->getUnitTest()->getNamespace(),
            '{model}' => $context->getModel()->getName(),
            '{modelInterface}' => $context->getModelInterface()->getName(),
            '{shortModel}' => $context->getModel()->getShortName(),
            '{shortModelInterface}' => $context->getModelInterface()->getShortName(),
            '{variable}' => $context->getVariableName(),
        ]);
    }

    protected function getTemplate(): string
    {
        return '<?php

namespace {namespace};

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use {model};
use {modelInterface};

class {shortModel}Test extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var {shortModel}
     */
    private ${variable};

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->{variable} = $this->objectManager->getObject({shortModel}::class);
    }

    public function testInstanceOf(): void
    {
        $this->assertInstanceOf({shortModelInterface}::class, $this->{variable});
    }
}
';
    }
}
