<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Test;

use IgorRain\CodeGenerator\Model\Generator\Test\ModelIntegrationTestFixtureGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Generator\AbstractTextSourceGeneratorTest;

/**
 * @internal
 * @coversNothing
 */
class ModelIntegrationTestFixtureGeneratorTest extends AbstractTextSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new ModelIntegrationTestFixtureGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

use Magento\TestFramework\Helper\Bootstrap;
use Vendor1\Module1Api\Api\Data\Menu\ItemInterface;
use Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();

/** @var ItemInterface $menuItem */
$menuItem = $objectManager->create(ItemInterface::class);
$menuItem->setId(333);

/** @var ItemRepositoryInterface $menuItemRepository */
$menuItemRepository = $objectManager->get(ItemRepositoryInterface::class);
$menuItemRepository->save($menuItem);
';
    }
}
