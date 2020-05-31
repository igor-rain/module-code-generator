<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\GraphQl;

use IgorRain\CodeGenerator\Model\Generator\GraphQl\SchemaGraphQlsGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\GraphQlSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Generator\GraphQl\SchemaGraphQlsGenerator
 */
class SchemaGraphQlsGeneratorTest extends TestCase
{
    public function testGenerateSchemaForNewFile(): void
    {
        $fileName = $this->getTmpFileName();
        unlink($fileName);

        $generator = $this->getGenerator($fileName);

        $context = ModelContextTest::createContext();
        $generator->generateSchema($fileName, $context);

        $this->assertEquals($this->getExpectedNewContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    public function testGenerateSchemaForExistingFile(): void
    {
        $fileName = $this->getTmpFileName();
        file_put_contents($fileName, $this->getExistingContent());

        $generator = $this->getGenerator($fileName);

        $context = ModelContextTest::createContext();
        $generator->generateSchema($fileName, $context);

        $this->assertEquals($this->getExpectedExistingContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    /**
     * @param $fileName
     *
     * @return SchemaGraphQlsGenerator
     */
    protected function getGenerator($fileName): SchemaGraphQlsGenerator
    {
        $source = new GraphQlSource($fileName);

        /** @var MockObject|SourceFactory $sourceFactory */
        $sourceFactory = $this->createMock(SourceFactory::class);
        $sourceFactory->expects($this->once())
            ->method('create')
            ->with($fileName, 'graphql')
            ->willReturn($source);

        return new SchemaGraphQlsGenerator($sourceFactory);
    }

    protected function getTmpFileName(): string
    {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    protected function getExpectedNewContent(): string
    {
        return 'type Query {
    menuItem(id: Int @doc(description: "Menu item id")): MenuItem @resolver(class: "Vendor1\\\\Module1GraphQl\\\\Model\\\\Resolver\\\\Menu\\\\Item") @doc(description: "The menu item query returns information about a menu item")
}

type MenuItem @doc(description: "Menu item information") {
    id: Int @doc(description: "Menu item id")
    sku: String @doc(description: "Menu item sku")
    name: String @doc(description: "Menu item name")
    description: String @doc(description: "Menu item description")
    price: Float @doc(description: "Menu item price")
    attribute_set_id: Int @doc(description: "Menu item attribute set id")
    is_visible: Boolean @doc(description: "Menu item is visible")
}
';
    }

    protected function getExistingContent(): string
    {
        return 'type Query {
    menu(id: Int @doc(description: "Menu id")): Menu @resolver(class: "Vendor1\\\\Module1GraphQl\\\\Model\\\\Resolver\\\\Menu") @doc(description: "The menu query returns information about a menu")
}

type Menu @doc(description: "Menu information") {
    sku: String @doc(description: "Menu sku")
    description: String @doc(description: "Menu description")
}

type MenuItem @doc(description: "Menu item information") {
    sku: String @doc(description: "Menu test item sku")
    test: String @doc(description: "Menu item test")
}
';
    }

    protected function getExpectedExistingContent(): string
    {
        return 'type Query {
    menu(id: Int @doc(description: "Menu id")): Menu @resolver(class: "Vendor1\\\\Module1GraphQl\\\\Model\\\\Resolver\\\\Menu") @doc(description: "The menu query returns information about a menu")
    menuItem(id: Int @doc(description: "Menu item id")): MenuItem @resolver(class: "Vendor1\\\\Module1GraphQl\\\\Model\\\\Resolver\\\\Menu\\\\Item") @doc(description: "The menu item query returns information about a menu item")
}

type Menu @doc(description: "Menu information") {
    sku: String @doc(description: "Menu sku")
    description: String @doc(description: "Menu description")
}

type MenuItem @doc(description: "Menu item information") {
    sku: String @doc(description: "Menu test item sku")
    test: String @doc(description: "Menu item test")
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
