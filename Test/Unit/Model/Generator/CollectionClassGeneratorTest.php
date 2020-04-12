<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator;

use IgorRain\CodeGenerator\Model\Generator\CollectionClassGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;

/**
 * @internal
 * @coversNothing
 */
class CollectionClassGeneratorTest extends AbstractPhpSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new CollectionClassGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1\Model\ResourceModel\Menu\Item;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Vendor1\Module1\Model\Menu\Item;
use Vendor1\Module1\Model\ResourceModel\Menu\Item as ItemResource;

class Collection extends AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = \'module1_menu_item_collection\';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject = \'menu_item_collection\';

    protected function _construct()
    {
        $this->_init(Item::class, ItemResource::class);
    }
}
';
    }
}
