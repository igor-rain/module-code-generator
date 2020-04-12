<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Test;

use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;

class RepositoryUnitTestGenerator
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
            '{namespace}' => $context->getRepository()->getUnitTest()->getNamespace(),
            '{resourceModel}' => $context->getResourceModel()->getName(),
            '{shortResourceModel}' => $context->getResourceModel()->getShortName() . 'Resource',
            '{collection}' => $context->getCollection()->getName(),
            '{model}' => $context->getModel()->getName(),
            '{modelInterface}' => $context->getModelInterface()->getName(),
            '{modelFactory}' => lcfirst($context->getModel()->getShortName() . 'Factory'),
            '{shortModel}' => $context->getModel()->getShortName(),
            '{shortModelInterface}' => $context->getModelInterface()->getShortName(),
            '{repository}' => $context->getRepository()->getName(),
            '{repositoryInterface}' => $context->getRepositoryInterface()->getName(),
            '{shortRepository}' => $context->getRepository()->getShortName(),
            '{shortRepositoryInterface}' => $context->getRepositoryInterface()->getShortName(),
            '{searchResultsInterface}' => $context->getSearchResultsInterface()->getName(),
            '{shortSearchResultsInterface}' => $context->getSearchResultsInterface()->getShortName(),
            '{variable}' => $context->getVariableName(),
            '{description}' => $context->getClassDescription(),
            '{descriptionCapital}' => ucfirst($context->getClassDescription()),
        ]);
    }

    protected function getTemplate(): string
    {
        return '<?php

namespace {namespace};

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use {model};
use {repository};
use {resourceModel} as {shortResourceModel};
use {collection};
use {collection}Factory;
use {modelInterface};
use {modelInterface}Factory;
use {searchResultsInterface};
use {searchResultsInterface}Factory;
use {repositoryInterface};

class {shortRepository}Test extends TestCase
{
    /**
     * @var {shortResourceModel}|MockObject
     */
    private $resource;
    /**
     * @var {shortModelInterface}Factory|MockObject
     */
    private ${modelFactory};
    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactory;
    /**
     * @var CollectionProcessorInterface|MockObject
     */
    private $collectionProcessor;
    /**
     * @var {shortSearchResultsInterface}Factory|MockObject
     */
    private $searchResultsFactory;
    /**
     * @var {shortRepository}
     */
    private $repository;

    public function setUp()
    {
        $this->resource = $this->createMock({shortResourceModel}::class);
        $this->{modelFactory} = $this->createMock({shortModelInterface}Factory::class);
        $this->collectionFactory = $this->createMock(CollectionFactory::class);
        $this->collectionProcessor = $this->createMock(CollectionProcessorInterface::class);
        $this->searchResultsFactory = $this->createMock({shortSearchResultsInterface}Factory::class);

        $this->repository = new {shortRepository}(
            $this->resource,
            $this->{modelFactory},
            $this->collectionFactory,
            $this->collectionProcessor,
            $this->searchResultsFactory
        );
    }

    public function testInstanceOf(): void
    {
        $this->assertInstanceOf({shortRepository}Interface::class, $this->repository);
    }

    public function testSave(): void
    {
        /** @var {shortModel}|MockObject ${variable} */
        ${variable} = $this->createMock({shortModel}::class);

        $this->resource
            ->expects($this->once())
            ->method(\'save\')
            ->with(${variable})
            ->willReturn(${variable});

        $this->assertSame(${variable}, $this->repository->save(${variable}));
    }

    /**
     * @expectedExceptionMessage Could not save {description}: could-not-save
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveWithException(): void
    {
        /** @var {shortModel}|MockObject ${variable} */
        ${variable} = $this->createMock({shortModel}::class);

        $this->resource
            ->expects($this->once())
            ->method(\'save\')
            ->with(${variable})
            ->willThrowException(new \RuntimeException(\'could-not-save\'));

        $this->repository->save(${variable});
    }

    public function testGetById(): void
    {
        ${variable}Id = \'333\';

        /** @var {shortModel}|MockObject ${variable} */
        ${variable} = $this->createMock({shortModel}::class);

        $this->{modelFactory}
            ->expects($this->once())
            ->method(\'create\')
            ->willReturn(${variable});

        $this->resource
            ->expects($this->once())
            ->method(\'load\')
            ->with(${variable}, ${variable}Id)
            ->willReturnCallback(static function (${variable}, ${variable}Id) {
                ${variable}->method(\'getId\')->willReturn(${variable}Id);
            });

        $this->assertSame(${variable}, $this->repository->getById(${variable}Id));
    }

    /**
     * @expectedExceptionMessage {descriptionCapital} with id "333" does not exist
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByIdWhenDoesNotExists(): void
    {
        ${variable}Id = \'333\';

        /** @var {shortModel}|MockObject ${variable} */
        ${variable} = $this->createMock({shortModel}::class);

        $this->{modelFactory}
            ->expects($this->once())
            ->method(\'create\')
            ->willReturn(${variable});

        $this->repository->getById(${variable}Id);
    }

    public function testDelete(): void
    {
        /** @var {shortModel}|MockObject ${variable} */
        ${variable} = $this->createMock({shortModel}::class);

        $this->resource
            ->expects($this->once())
            ->method(\'delete\')
            ->with(${variable})
            ->willReturn(${variable});

        $this->repository->delete(${variable});
    }

    /**
     * @expectedExceptionMessage Could not delete {description}: could-not-delete
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteWithException(): void
    {
        /** @var {shortModel}|MockObject ${variable} */
        ${variable} = $this->createMock({shortModel}::class);

        $this->resource
            ->expects($this->once())
            ->method(\'delete\')
            ->with(${variable})
            ->willThrowException(new \RuntimeException(\'could-not-delete\'));

        $this->repository->delete(${variable});
    }

    public function testDeleteById(): void
    {
        ${variable}Id = \'333\';

        /** @var {shortModel}|MockObject ${variable} */
        ${variable} = $this->createMock({shortModel}::class);

        /** @var {shortRepository}|MockObject $repository */
        $repository = $this->createPartialMock({shortRepository}::class, [
            \'delete\',
            \'getById\'
        ]);

        $repository->expects($this->once())
            ->method(\'getById\')
            ->with(${variable}Id)
            ->willReturn(${variable});

        $repository->expects($this->once())
            ->method(\'delete\')
            ->with(${variable});

        $repository->deleteById(${variable}Id);
    }

    public function testGetList(): void
    {
        $items = [
            $this->createMock({shortModelInterface}::class),
            $this->createMock({shortModelInterface}::class),
            $this->createMock({shortModelInterface}::class)
        ];
        $itemsTotalCount = 50;

        /** @var SearchCriteriaInterface|MockObject $searchCriteria */
        $searchCriteria = $this->createMock(SearchCriteriaInterface::class);
        /** @var Collection|MockObject $collection */
        $collection = $this->createMock(Collection::class);
        /** @var {shortSearchResultsInterface}|MockObject $searchResults */
        $searchResults = $this->createMock({shortSearchResultsInterface}::class);

        $this->collectionProcessor->expects($this->once())
            ->method(\'process\')
            ->with($searchCriteria, $collection);

        $this->collectionFactory->expects($this->once())
            ->method(\'create\')
            ->willReturn($collection);

        $this->searchResultsFactory->expects($this->once())
            ->method(\'create\')
            ->willReturn($searchResults);

        $collection->expects($this->once())
            ->method(\'getItems\')
            ->willReturn($items);

        $collection->expects($this->once())
            ->method(\'getSize\')
            ->willReturn($itemsTotalCount);

        $searchResults->expects($this->once())
            ->method(\'setSearchCriteria\')
            ->with($searchCriteria);

        $searchResults->expects($this->once())
            ->method(\'setItems\')
            ->with($items);

        $searchResults->expects($this->once())
            ->method(\'setTotalCount\')
            ->with($itemsTotalCount);

        $this->assertSame($searchResults, $this->repository->getList($searchCriteria));
    }
}
';
    }
}
