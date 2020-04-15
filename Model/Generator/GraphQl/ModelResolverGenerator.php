<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\GraphQl;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;

class ModelResolverGenerator
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
            '{namespace}' => $context->getGraphQlModelResolver()->getNamespace(),
            '{class}' => $context->getGraphQlModelResolver()->getShortName(),
            '{dataProvider}' => $context->getGraphQlModelDataProvider()->getName(),
            '{variable}' => $context->getVariableName(),
            '{descriptionCapital}' => ucfirst($context->getClassDescription()),
        ]);
    }

    protected function getTemplate(): string
    {
        return '<?php

namespace {namespace};

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use {dataProvider} as DataProvider;

class {class} implements ResolverInterface
{
    /**
     * @var DataProvider
     */
    private $dataProvider;

    public function __construct(
        DataProvider $dataProvider
    ) {
        $this->dataProvider = $dataProvider;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args[\'id\'])) {
            throw new GraphQlInputException(__(\'"{descriptionCapital} id should be specified\'));
        }

        ${variable}Data = [];

        try {
            if (isset($args[\'id\'])) {
                ${variable}Data = $this->dataProvider->getDataById($args[\'id\']);
            }
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return ${variable}Data;
    }
}
';
    }
}
