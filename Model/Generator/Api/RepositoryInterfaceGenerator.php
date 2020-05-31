<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Api;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use PhpParser\BuilderFactory;
use PhpParser\Node;

class RepositoryInterfaceGenerator
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
        /** @var PhpSource $source */
        $source = $this->sourceFactory->create($fileName, 'php');
        $source->setStmts($this->prepareData($context));
        $source->save();
    }

    protected function prepareData(ModelContext $context): array
    {
        $factory = new BuilderFactory();
        $interface = $factory->interface($context->getRepositoryInterface()->getShortName());
        $interface->setDocComment('/**
            * @api
            */');

        $saveMethod = $factory->method('save')
            ->makePublic()
            ->addParam($factory->param($context->getVariableName())->setType($context->getModelInterface()->getShortName()))
            ->setReturnType($context->getModelInterface()->getShortName())
            ->setDocComment('/**
                    * Save ' . $context->getClassDescription() . '
                    *
                    * @param \\' . $context->getModelInterface()->getName() . ' $' . $context->getVariableName() . '
                    * @return \\' . $context->getModelInterface()->getName() . '
                    * @throws \Magento\Framework\Exception\CouldNotSaveException
                    */');

        $getByIdMethod = $factory->method('getById')
            ->makePublic()
            ->addParam($factory->param($context->getVariableName() . 'Id')->setType(new Node\Name($context->getPrimaryField()->getPhpType())))
            ->setReturnType($context->getModelInterface()->getShortName())
            ->setDocComment('/**
                    * Get ' . $context->getClassDescription() . ' by id
                    *
                    * @param ' . $context->getPrimaryField()->getPhpType() . ' $' . $context->getVariableName() . 'Id
                    * @return \\' . $context->getModelInterface()->getName() . '
                    * @throws \Magento\Framework\Exception\NoSuchEntityException
                    */');

        $deleteMethod = $factory->method('delete')
            ->makePublic()
            ->addParam($factory->param($context->getVariableName())->setType($context->getModelInterface()->getShortName()))
            ->setReturnType('void')
            ->setDocComment('/**
                    * Delete ' . $context->getClassDescription() . '
                    *
                    * @param \\' . $context->getModelInterface()->getName() . ' $' . $context->getVariableName() . '
                    * @return void
                    * @throws \Magento\Framework\Exception\CouldNotDeleteException
                    */');

        $deleteByIdMethod = $factory->method('deleteById')
            ->makePublic()
            ->addParam($factory->param($context->getVariableName() . 'Id')->setType(new Node\Name($context->getPrimaryField()->getPhpType())))
            ->setReturnType('void')
            ->setDocComment('/**
                    * Delete ' . $context->getClassDescription() . ' by id
                    *
                    * @param ' . $context->getPrimaryField()->getPhpType() . ' $' . $context->getVariableName() . 'Id
                    * @return void
                    * @throws \Magento\Framework\Exception\NoSuchEntityException
                    * @throws \Magento\Framework\Exception\CouldNotDeleteException
                    */');

        $getListMethod = $factory->method('getList')
            ->makePublic()
            ->addParam($factory->param('searchCriteria')->setType('SearchCriteriaInterface'))
            ->setReturnType($context->getSearchResultsInterface()->getShortName())
            ->setDocComment('/**
                    * Get ' . $context->getClassDescription() . ' list
                    *
                    * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
                    * @return \\' . $context->getSearchResultsInterface()->getName() . '
                    */');

        $interface->addStmt($saveMethod);
        $interface->addStmt($getByIdMethod);
        $interface->addStmt($deleteMethod);
        $interface->addStmt($deleteByIdMethod);
        $interface->addStmt($getListMethod);

        $node = $factory->namespace($context->getRepositoryInterface()->getNamespace())
            ->addStmt($factory->use(SearchCriteriaInterface::class))
            ->addStmt($factory->use($context->getModelInterface()->getName()))
            ->addStmt($factory->use($context->getSearchResultsInterface()->getName()))
            ->addStmt($interface)
            ->getNode();

        return [$node];
    }
}
