<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Test;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;

class ModelIntegrationTestFixtureGenerator
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
            '{modelInterface}' => $context->getModelInterface()->getName(),
            '{shortModelInterface}' => $context->getModelInterface()->getShortName(),
            '{repositoryInterface}' => $context->getRepositoryInterface()->getName(),
            '{shortRepositoryInterface}' => $context->getRepositoryInterface()->getShortName(),
            '{variable}' => $context->getVariableName(),
        ]);
    }

    protected function getTemplate(): string
    {
        return '<?php

use Magento\TestFramework\Helper\Bootstrap;
use {modelInterface};
use {repositoryInterface};

$objectManager = Bootstrap::getObjectManager();

/** @var {shortModelInterface} ${variable} */
${variable} = $objectManager->create({shortModelInterface}::class);
${variable}->setId(333);

/** @var {shortRepositoryInterface} ${variable}Repository */
${variable}Repository = $objectManager->get({shortRepositoryInterface}::class);
${variable}Repository->save(${variable});
';
    }
}
