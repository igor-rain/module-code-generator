<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\GraphQlSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\ResourceModel\Source\GraphQlSource
 */
class GraphQlSourceTest extends TestCase
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var PhpSource
     */
    private $source;

    public function setUp(): void
    {
        $this->fileName = tempnam(sys_get_temp_dir(), 'test');
        $this->source = new GraphQlSource($this->fileName);
    }

    public function tearDown(): void
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
    }

    public function testLoadSave(): void
    {
        file_put_contents($this->fileName, $this->getSampleGraphQl());
        $this->source->load();
        $this->source->save();
        $this->assertEquals($this->getSampleGraphQl(), file_get_contents($this->fileName));
    }

    public function testSaveEmpty(): void
    {
        $this->source->save();
        $this->assertEquals(PHP_EOL, file_get_contents($this->fileName));
    }

    protected function getSampleGraphQl(): string
    {
        return 'type Query {
    menuItem(id: Int @doc(description: "Menu item id")): MenuItem @resolver(class: "Vendor1\\\\Module1GraphQl\\\\Model\\\\Resolver\\\\Menu\\\\Item") @doc(description: "The menu item query returns information about a menu item")
}

type MenuItem @doc(description: "Menu item information") {
    id: Int @doc(description: "Menu item id")
    name: String @doc(description: "Menu item name")
    description: String @doc(description: "Menu item description")
    price: Float @doc(description: "Menu item price")
    attribute_set_id: Int @doc(description: "Menu item attribute set id")
    is_visible: Boolean @doc(description: "Menu item is visible")
}
';
    }
}
