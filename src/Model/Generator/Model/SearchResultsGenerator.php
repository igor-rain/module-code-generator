<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Model;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use Magento\Framework\Api\SearchResults;
use PhpParser\BuilderFactory;

class SearchResultsGenerator
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
        $class = $factory->class($context->getSearchResults()->getShortName());
        $class->extend('SearchResults');
        $class->implement($context->getSearchResultsInterface()->getShortName());

        $node = $factory->namespace($context->getSearchResults()->getNamespace())
            ->addStmt($factory->use(SearchResults::class))
            ->addStmt($factory->use($context->getSearchResultsInterface()->getName()))
            ->addStmt($class)
            ->getNode();

        return [$node];
    }
}
