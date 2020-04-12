<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;

class RepositoryClassGenerator
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
            '{namespace}' => $context->getRepository()->getNamespace(),
            '{resourceModel}' => $context->getResourceModel()->getName(),
            '{shortResourceModel}' => $context->getResourceModel()->getShortName() . 'Resource',
            '{collection}' => $context->getCollection()->getName(),
            '{modelInterface}' => $context->getModelInterface()->getName(),
            '{searchResultsInterface}' => $context->getSearchResultsInterface()->getName(),
            '{repositoryInterface}' => $context->getRepositoryInterface()->getName(),
            '{shortRepository}' => $context->getRepository()->getShortName(),
            '{shortRepositoryInterface}' => $context->getRepositoryInterface()->getShortName(),
            '{shortModelInterface}' => $context->getModelInterface()->getShortName(),
            '{modelFactory}' => lcfirst($context->getModel()->getShortName() . 'Factory'),
            '{shortSearchResultsInterface}' => $context->getSearchResultsInterface()->getShortName(),
            '{variable}' => $context->getVariableName(),
            '{description}' => $context->getClassDescription(),
            '{descriptionCapital}' => ucfirst($context->getClassDescription()),
        ]);
    }

    protected function getTemplate()
    {
        return '<?php

namespace {namespace};

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use {resourceModel} as {shortResourceModel};
use {collection}Factory;
use {modelInterface};
use {modelInterface}Factory;
use {searchResultsInterface};
use {searchResultsInterface}Factory;
use {repositoryInterface};

class {shortRepository} implements {shortRepositoryInterface}
{
    /**
     * @var {shortResourceModel}
     */
    protected $resource;
    /**
     * @var {shortModelInterface}Factory
     */
    protected ${modelFactory};
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;
    /**
     * @var {shortSearchResultsInterface}Factory
     */
    protected $searchResultsFactory;

    public function __construct(
        {shortResourceModel} $resource,
        {shortModelInterface}Factory ${modelFactory},
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        {shortSearchResultsInterface}Factory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->{modelFactory} = ${modelFactory};
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function save({shortModelInterface} ${variable}): {shortModelInterface}
    {
        try {
            $this->resource->save(${variable});
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(\'Could not save {description}: %1\', $exception->getMessage()));
        }
        return ${variable};
    }

    public function getById(${variable}Id): {shortModelInterface}
    {
        ${variable} = $this->{modelFactory}->create();
        $this->resource->load(${variable}, ${variable}Id);
        if (!${variable}->getId()) {
            throw new NoSuchEntityException(__(\'{descriptionCapital} with id "%1" does not exist\', ${variable}Id));
        }
        return ${variable};
    }

    public function delete({shortModelInterface} ${variable}): void
    {
        try {
            $this->resource->delete(${variable});
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(\'Could not delete {description}: %1\', $exception->getMessage()));
        }
    }

    public function deleteById(${variable}Id): void
    {
        $this->delete($this->getById(${variable}Id));
    }

    public function getList(SearchCriteriaInterface $searchCriteria): {shortSearchResultsInterface}
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}
';
    }
}
