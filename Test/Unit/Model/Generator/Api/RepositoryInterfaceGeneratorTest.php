<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Api;

use IgorRain\CodeGenerator\Model\Generator\Api\RepositoryInterfaceGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Generator\AbstractPhpSourceGeneratorTest;

/**
 * @internal
 * @coversNothing
 */
class RepositoryInterfaceGeneratorTest extends AbstractPhpSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new RepositoryInterfaceGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1Api\Api\Menu;

use Magento\Framework\Api\SearchCriteriaInterface;
use Vendor1\Module1Api\Api\Data\Menu\ItemInterface;
use Vendor1\Module1Api\Api\Data\Menu\ItemSearchResultsInterface;

/**
 * @api
 */
interface ItemRepositoryInterface
{
    /**
     * Save menu item
     *
     * @param \Vendor1\Module1Api\Api\Data\Menu\ItemInterface $menuItem
     * @return \Vendor1\Module1Api\Api\Data\Menu\ItemInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ItemInterface $menuItem): ItemInterface;

    /**
     * Get menu item by id
     *
     * @param string $menuItemId
     * @return \Vendor1\Module1Api\Api\Data\Menu\ItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($menuItemId): ItemInterface;

    /**
     * Delete menu item
     *
     * @param \Vendor1\Module1Api\Api\Data\Menu\ItemInterface $menuItem
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ItemInterface $menuItem): void;

    /**
     * Delete menu item by id
     *
     * @param string $menuItemId
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($menuItemId): void;

    /**
     * Get menu item list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vendor1\Module1Api\Api\Data\Menu\ItemSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ItemSearchResultsInterface;
}
';
    }
}
