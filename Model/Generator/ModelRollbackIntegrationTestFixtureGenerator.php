<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;

class ModelRollbackIntegrationTestFixtureGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generate($fileName, ModelContext $context)
    {
        /** @var TextSource $source */
        $source = $this->sourceFactory->create($fileName, 'text');
        $source->setContent($this->prepareData($context));
        $source->save();
    }

    protected function prepareData(ModelContext $context)
    {
        return strtr($this->getTemplate(), [
            '{repositoryInterface}' => $context->getRepositoryInterface()->getName(),
            '{shortRepositoryInterface}' => $context->getRepositoryInterface()->getShortName(),
            '{variable}' => $context->getVariableName(),
        ]);
    }

    protected function getTemplate()
    {
        return '<?php

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use {repositoryInterface};

$objectManager = Bootstrap::getObjectManager();

try {
    /** @var {shortRepositoryInterface} ${variable}Repository */
    ${variable}Repository = $objectManager->get({shortRepositoryInterface}::class);
    ${variable}Repository->deleteById(333);
} catch (NoSuchEntityException $exception) {
}
';
    }
}
