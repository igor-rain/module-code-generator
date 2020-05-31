<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\GraphQl;

use IgorRain\CodeGenerator\Model\Generator\GraphQl\ModelDataProviderGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Generator\AbstractTextSourceGeneratorTest;

/**
 * @covers \IgorRain\CodeGenerator\Model\Generator\GraphQl\ModelDataProviderGenerator
 */
class ModelDataProviderGeneratorTest extends AbstractTextSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new ModelDataProviderGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1GraphQl\Model\Resolver\DataProvider\Menu;

use Magento\Framework\Exception\NoSuchEntityException;
use Vendor1\Module1Api\Api\Data\Menu\ItemInterface;
use Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface;

class Item
{
    /**
     * @var ItemRepositoryInterface
     */
    protected $repository;

    public function __construct(
        ItemRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Returns menu item data by id
     *
     * @param int $menuItemId
     * @return array
     * @throws NoSuchEntityException
     */
    public function getDataById(int $menuItemId): array
    {
        $page = $this->repository->getById($menuItemId);

        return $this->convertData($page);
    }

    /**
     * Convert menu item data
     *
     * @param ItemInterface $menuItem
     * @return array
     */
    private function convertData(ItemInterface $menuItem): array
    {
        return [
            \'id\' => $menuItem->getId(),
            ItemInterface::SKU => $menuItem->getSku(),
            ItemInterface::NAME => $menuItem->getName(),
            ItemInterface::DESCRIPTION => $menuItem->getDescription(),
            ItemInterface::PRICE => $menuItem->getPrice(),
            ItemInterface::ATTRIBUTE_SET_ID => $menuItem->getAttributeSetId(),
            ItemInterface::IS_VISIBLE => $menuItem->getIsVisible(),
        ];
    }
}
';
    }
}
