<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Model;

use IgorRain\CodeGenerator\Model\Generator\Model\RepositoryGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Generator\AbstractTextSourceGeneratorTest;

/**
 * @internal
 * @coversNothing
 */
class RepositoryGeneratorTest extends AbstractTextSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new RepositoryGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1\Model\Menu;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vendor1\Module1\Model\ResourceModel\Menu\Item as ItemResource;
use Vendor1\Module1\Model\ResourceModel\Menu\Item\CollectionFactory;
use Vendor1\Module1Api\Api\Data\Menu\ItemInterface;
use Vendor1\Module1Api\Api\Data\Menu\ItemInterfaceFactory;
use Vendor1\Module1Api\Api\Data\Menu\ItemSearchResultsInterface;
use Vendor1\Module1Api\Api\Data\Menu\ItemSearchResultsInterfaceFactory;
use Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface;

class ItemRepository implements ItemRepositoryInterface
{
    /**
     * @var ItemResource
     */
    protected $resource;
    /**
     * @var ItemInterfaceFactory
     */
    protected $itemFactory;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;
    /**
     * @var ItemSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    public function __construct(
        ItemResource $resource,
        ItemInterfaceFactory $itemFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ItemSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->itemFactory = $itemFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function save(ItemInterface $menuItem): ItemInterface
    {
        try {
            $this->resource->save($menuItem);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(\'Could not save menu item: %1\', $exception->getMessage()));
        }
        return $menuItem;
    }

    public function getById($menuItemId): ItemInterface
    {
        $menuItem = $this->itemFactory->create();
        $this->resource->load($menuItem, $menuItemId);
        if (!$menuItem->getId()) {
            throw new NoSuchEntityException(__(\'Menu item with id "%1" does not exist\', $menuItemId));
        }
        return $menuItem;
    }

    public function delete(ItemInterface $menuItem): void
    {
        try {
            $this->resource->delete($menuItem);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(\'Could not delete menu item: %1\', $exception->getMessage()));
        }
    }

    public function deleteById($menuItemId): void
    {
        $this->delete($this->getById($menuItemId));
    }

    public function getList(SearchCriteriaInterface $searchCriteria): ItemSearchResultsInterface
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
