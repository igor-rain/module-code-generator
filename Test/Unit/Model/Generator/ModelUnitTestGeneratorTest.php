<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator;

use IgorRain\CodeGenerator\Model\Generator\ModelUnitTestGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;

/**
 * @internal
 * @coversNothing
 */
class ModelUnitTestGeneratorTest extends AbstractTextSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new ModelUnitTestGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1\Test\Unit\Model\Menu;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Vendor1\Module1\Model\Menu\Item;
use Vendor1\Module1Api\Api\Data\Menu\ItemInterface;

class ItemTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var Item
     */
    private $menuItem;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->menuItem = $this->objectManager->getObject(Item::class);
    }

    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(ItemInterface::class, $this->menuItem);
    }
}
';
    }
}
