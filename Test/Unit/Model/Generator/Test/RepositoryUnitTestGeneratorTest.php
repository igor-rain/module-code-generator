<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Test;

use IgorRain\CodeGenerator\Model\Generator\Test\RepositoryUnitTestGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Generator\AbstractTextSourceGeneratorTest;

/**
 * @internal
 * @coversNothing
 */
class RepositoryUnitTestGeneratorTest extends AbstractTextSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new RepositoryUnitTestGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1\Test\Unit\Model\Menu;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vendor1\Module1\Model\Menu\Item;
use Vendor1\Module1\Model\Menu\ItemRepository;
use Vendor1\Module1\Model\ResourceModel\Menu\Item as ItemResource;
use Vendor1\Module1\Model\ResourceModel\Menu\Item\Collection;
use Vendor1\Module1\Model\ResourceModel\Menu\Item\CollectionFactory;
use Vendor1\Module1Api\Api\Data\Menu\ItemInterface;
use Vendor1\Module1Api\Api\Data\Menu\ItemInterfaceFactory;
use Vendor1\Module1Api\Api\Data\Menu\ItemSearchResultsInterface;
use Vendor1\Module1Api\Api\Data\Menu\ItemSearchResultsInterfaceFactory;
use Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface;

class ItemRepositoryTest extends TestCase
{
    /**
     * @var ItemResource|MockObject
     */
    private $resource;
    /**
     * @var ItemInterfaceFactory|MockObject
     */
    private $itemFactory;
    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactory;
    /**
     * @var CollectionProcessorInterface|MockObject
     */
    private $collectionProcessor;
    /**
     * @var ItemSearchResultsInterfaceFactory|MockObject
     */
    private $searchResultsFactory;
    /**
     * @var ItemRepository
     */
    private $repository;

    public function setUp()
    {
        $this->resource = $this->createMock(ItemResource::class);
        $this->itemFactory = $this->createMock(ItemInterfaceFactory::class);
        $this->collectionFactory = $this->createMock(CollectionFactory::class);
        $this->collectionProcessor = $this->createMock(CollectionProcessorInterface::class);
        $this->searchResultsFactory = $this->createMock(ItemSearchResultsInterfaceFactory::class);

        $this->repository = new ItemRepository(
            $this->resource,
            $this->itemFactory,
            $this->collectionFactory,
            $this->collectionProcessor,
            $this->searchResultsFactory
        );
    }

    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(ItemRepositoryInterface::class, $this->repository);
    }

    public function testSave(): void
    {
        /** @var Item|MockObject $menuItem */
        $menuItem = $this->createMock(Item::class);

        $this->resource
            ->expects($this->once())
            ->method(\'save\')
            ->with($menuItem)
            ->willReturn($menuItem);

        $this->assertSame($menuItem, $this->repository->save($menuItem));
    }

    /**
     * @expectedExceptionMessage Could not save menu item: could-not-save
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveWithException(): void
    {
        /** @var Item|MockObject $menuItem */
        $menuItem = $this->createMock(Item::class);

        $this->resource
            ->expects($this->once())
            ->method(\'save\')
            ->with($menuItem)
            ->willThrowException(new \RuntimeException(\'could-not-save\'));

        $this->repository->save($menuItem);
    }

    public function testGetById(): void
    {
        $menuItemId = \'333\';

        /** @var Item|MockObject $menuItem */
        $menuItem = $this->createMock(Item::class);

        $this->itemFactory
            ->expects($this->once())
            ->method(\'create\')
            ->willReturn($menuItem);

        $this->resource
            ->expects($this->once())
            ->method(\'load\')
            ->with($menuItem, $menuItemId)
            ->willReturnCallback(static function ($menuItem, $menuItemId) {
                $menuItem->method(\'getId\')->willReturn($menuItemId);
            });

        $this->assertSame($menuItem, $this->repository->getById($menuItemId));
    }

    /**
     * @expectedExceptionMessage Menu item with id "333" does not exist
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByIdWhenDoesNotExists(): void
    {
        $menuItemId = \'333\';

        /** @var Item|MockObject $menuItem */
        $menuItem = $this->createMock(Item::class);

        $this->itemFactory
            ->expects($this->once())
            ->method(\'create\')
            ->willReturn($menuItem);

        $this->repository->getById($menuItemId);
    }

    public function testDelete(): void
    {
        /** @var Item|MockObject $menuItem */
        $menuItem = $this->createMock(Item::class);

        $this->resource
            ->expects($this->once())
            ->method(\'delete\')
            ->with($menuItem)
            ->willReturn($menuItem);

        $this->repository->delete($menuItem);
    }

    /**
     * @expectedExceptionMessage Could not delete menu item: could-not-delete
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteWithException(): void
    {
        /** @var Item|MockObject $menuItem */
        $menuItem = $this->createMock(Item::class);

        $this->resource
            ->expects($this->once())
            ->method(\'delete\')
            ->with($menuItem)
            ->willThrowException(new \RuntimeException(\'could-not-delete\'));

        $this->repository->delete($menuItem);
    }

    public function testDeleteById(): void
    {
        $menuItemId = \'333\';

        /** @var Item|MockObject $menuItem */
        $menuItem = $this->createMock(Item::class);

        /** @var ItemRepository|MockObject $repository */
        $repository = $this->createPartialMock(ItemRepository::class, [
            \'delete\',
            \'getById\'
        ]);

        $repository->expects($this->once())
            ->method(\'getById\')
            ->with($menuItemId)
            ->willReturn($menuItem);

        $repository->expects($this->once())
            ->method(\'delete\')
            ->with($menuItem);

        $repository->deleteById($menuItemId);
    }

    public function testGetList(): void
    {
        $items = [
            $this->createMock(ItemInterface::class),
            $this->createMock(ItemInterface::class),
            $this->createMock(ItemInterface::class)
        ];
        $itemsTotalCount = 50;

        /** @var SearchCriteriaInterface|MockObject $searchCriteria */
        $searchCriteria = $this->createMock(SearchCriteriaInterface::class);
        /** @var Collection|MockObject $collection */
        $collection = $this->createMock(Collection::class);
        /** @var ItemSearchResultsInterface|MockObject $searchResults */
        $searchResults = $this->createMock(ItemSearchResultsInterface::class);

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
