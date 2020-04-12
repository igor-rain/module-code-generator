<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Test;

use IgorRain\CodeGenerator\Model\Generator\Test\RepositoryIntegrationTestGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Generator\AbstractTextSourceGeneratorTest;

/**
 * @internal
 * @coversNothing
 */
class RepositoryIntegrationTestGeneratorTest extends AbstractTextSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new RepositoryIntegrationTestGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1\Test\Integration\Model\Menu;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Vendor1\Module1Api\Api\Data\Menu\ItemInterfaceFactory;
use Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface;

class ItemRepositoryTest extends TestCase
{
    public const EXISTING_ID = 333;
    public const NEW_ID = 444;
    public const MISSING_ID = 555;

    /**
     * @var ItemRepositoryInterface
     */
    private $repository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ItemInterfaceFactory
     */
    private $itemFactory;

    public function setUp()
    {
        $this->repository = Bootstrap::getObjectManager()->create(ItemRepositoryInterface::class);
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);
        $this->itemFactory = Bootstrap::getObjectManager()->get(ItemInterfaceFactory::class);
    }

    public function testSave(): void
    {
        $menuItem = $this->itemFactory->create();
        $menuItem->setId(self::NEW_ID);

        $this->repository->save($menuItem);

        $menuItemLoaded = $this->repository->getById(self::NEW_ID);
        $this->assertEquals(self::NEW_ID, $menuItemLoaded->getId());
        $this->repository->delete($menuItemLoaded);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Vendor1/Module1/Test/Integration/_files/menu_item.php
     */
    public function testGetById(): void
    {
        $menuItem = $this->repository->getById(self::EXISTING_ID);
        $this->assertEquals(self::EXISTING_ID, $menuItem->getId());
    }

    /**
     * @expectedExceptionMessage Menu item with id "555" does not exist
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByIdWhenDoesNotExists(): void
    {
        $this->repository->getById(self::MISSING_ID);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Vendor1/Module1/Test/Integration/_files/menu_item.php
     * @expectedExceptionMessage Menu item with id "333" does not exist
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDelete(): void
    {
        $menuItem = $this->repository->getById(self::EXISTING_ID);
        $this->repository->delete($menuItem);
        $this->repository->getById(self::EXISTING_ID);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Vendor1/Module1/Test/Integration/_files/menu_item.php
     * @expectedExceptionMessage Menu item with id "333" does not exist
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteById(): void
    {
        $this->repository->deleteById(self::EXISTING_ID);
        $this->repository->getById(self::EXISTING_ID);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Vendor1/Module1/Test/Integration/_files/menu_item.php
     */
    public function testGetList(): void
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\'entity_id\', self::EXISTING_ID, \'eq\')
            ->create();
        $list = $this->repository->getList($searchCriteria);
        $count = $list->getTotalCount();
        $items = $list->getItems();

        $this->assertSame(1, $count);
        $this->assertCount(1, $items);

        $firstItem = reset($items);
        $this->assertEquals(self::EXISTING_ID, $firstItem->getId());
    }
}
';
    }
}
