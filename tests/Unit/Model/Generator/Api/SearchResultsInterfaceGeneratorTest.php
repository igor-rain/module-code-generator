<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Api;

use IgorRain\CodeGenerator\Model\Generator\Api\SearchResultsInterfaceGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Generator\AbstractPhpSourceGeneratorTest;

/**
 * @covers \IgorRain\CodeGenerator\Model\Generator\Api\SearchResultsInterfaceGenerator
 */
class SearchResultsInterfaceGeneratorTest extends AbstractPhpSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new SearchResultsInterfaceGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1Api\Api\Data\Menu;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface ItemSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get menu item list
     *
     * @return \Vendor1\Module1Api\Api\Data\Menu\ItemInterface[]
     */
    public function getItems();

    /**
     * Set menu item list
     *
     * @param \Vendor1\Module1Api\Api\Data\Menu\ItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
';
    }
}
