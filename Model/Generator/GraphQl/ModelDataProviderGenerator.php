<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\GraphQl;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;

class ModelDataProviderGenerator
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
            '{namespace}' => $context->getGraphQlModelDataProvider()->getNamespace(),
            '{class}' => $context->getGraphQlModelDataProvider()->getShortName(),
            '{modelInterface}' => $context->getModelInterface()->getName(),
            '{shortModelInterface}' => $context->getModelInterface()->getShortName(),
            '{repositoryInterface}' => $context->getRepositoryInterface()->getName(),
            '{shortRepositoryInterface}' => $context->getRepositoryInterface()->getShortName(),
            '{variable}' => $context->getVariableName(),
            '{description}' => $context->getClassDescription(),
            '{data}' => $this->getConvertDataCode($context),
        ]);
    }

    protected function getConvertDataCode(ModelContext $context): string
    {
        $code = '';
        foreach ($context->getFields() as $field) {
            $code .= '            ';
            if ($field->getIsPrimary()) {
                $code .= '\'id\' => $' . $context->getVariableName() . '->getId()';
            } else {
                $code .= $context->getModelInterface()->getShortName()
                    . '::'
                    . $field->getConstantName()
                    . ' => $'
                    . $context->getVariableName()
                    . '->'
                    . $field->getMethodName('get')
                    . '()';
            }
            $code .= ",\n";
        }
        return rtrim($code);
    }

    protected function getTemplate(): string
    {
        return '<?php

namespace {namespace};

use Magento\Framework\Exception\NoSuchEntityException;
use {modelInterface};
use {repositoryInterface};

class {class}
{
    /**
     * @var {shortRepositoryInterface}
     */
    protected $repository;

    public function __construct(
        {shortRepositoryInterface} $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Returns {description} data by id
     *
     * @param int ${variable}Id
     * @return array
     * @throws NoSuchEntityException
     */
    public function getDataById(int ${variable}Id): array
    {
        $page = $this->repository->getById(${variable}Id);

        return $this->convertData($page);
    }

    /**
     * Convert {description} data
     *
     * @param {shortModelInterface} ${variable}
     * @return array
     */
    private function convertData({shortModelInterface} ${variable}): array
    {
        return [
{data}
        ];
    }
}
';
    }
}
