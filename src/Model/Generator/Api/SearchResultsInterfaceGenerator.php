<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Api;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use Magento\Framework\Api\SearchResultsInterface;
use PhpParser\BuilderFactory;

class SearchResultsInterfaceGenerator
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
        $interface = $factory->interface($context->getSearchResultsInterface()->getShortName());
        $interface->extend('SearchResultsInterface');
        $interface->setDocComment('/**
            * @api
            */');

        $getMethod = $factory->method('getItems')
            ->makePublic()
            ->setDocComment('/**
                    * Get ' . $context->getClassDescription() . ' list
                    *
                    * @return \\' . $context->getModelInterface()->getName() . '[]
                    */');

        $setMethod = $factory->method('setItems')
            ->makePublic()
            ->addParam($factory->param('items')->setType('array'))
            ->setDocComment('/**
                    * Set ' . $context->getClassDescription() . ' list
                    *
                    * @param \\' . $context->getModelInterface()->getName() . '[] $items
                    * @return $this
                    */');

        $interface->addStmt($getMethod);
        $interface->addStmt($setMethod);

        $node = $factory->namespace($context->getSearchResultsInterface()->getNamespace())
            ->addStmt($factory->use(SearchResultsInterface::class))
            ->addStmt($interface)
            ->getNode();

        return [$node];
    }
}
