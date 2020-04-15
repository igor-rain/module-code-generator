<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\GraphQlSource;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class GraphQlSourceTest extends TestCase
{
    public function testLoadMissingFile(): void
    {
        $this->expectException('RuntimeException');
        $graphQlSource = new GraphQlSource('/tmp/missing-file');
        $graphQlSource->load();
    }

    public function testLoadSave(): void
    {
        $fileName = $this->getTmpFileName();
        file_put_contents($fileName, $this->getSampleGraphQl());

        $graphQlSource = new GraphQlSource($fileName);
        $graphQlSource->load();
        $graphQlSource->save();

        $this->assertEquals($this->getSampleGraphQl(), file_get_contents($fileName));
        unlink($fileName);
    }

    protected function getTmpFileName()
    {
        return tempnam(sys_get_temp_dir(), 'test');
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
