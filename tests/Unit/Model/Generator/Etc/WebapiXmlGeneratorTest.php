<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Etc;

use IgorRain\CodeGenerator\Model\Generator\Etc\WebapiXmlGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Generator\Etc\WebapiXmlGenerator
 * @covers \IgorRain\CodeGenerator\Model\Generator\Etc\AbstractXmlGenerator
 */
class WebapiXmlGeneratorTest extends TestCase
{
    public function testGenerateModelRoutes(): void
    {
        $fileName = '/tmp/filename';
        $context = ModelContextTest::createContext();

        /** @var WebapiXmlGenerator|MockObject $generator */
        $generator = $this->createPartialMock(WebapiXmlGenerator::class, [
            'generateRoute'
        ]);

        $generator->expects($this->exactly(4))
            ->method('generateRoute')
            ->withConsecutive([
                $fileName,
                '/V1/menuItem/:menuItemId',
                'GET',
                'Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface',
                'getById',
                ['Vendor1_Module1::menu_item'],
            ], [
                $fileName,
                '/V1/menuItem',
                'POST',
                'Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface',
                'save',
                ['Vendor1_Module1::menu_item'],
            ], [
                $fileName,
                '/V1/menuItem/:menuItemId',
                'DELETE',
                'Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface',
                'deleteById',
                ['Vendor1_Module1::menu_item'],
            ], [
                $fileName,
                '/V1/menuItem/list',
                'GET',
                'Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface',
                'getList',
                ['Vendor1_Module1::menu_item'],
            ]);

        $generator->generateModelRoutes($fileName, $context);
    }

    public function testGenerateRouteForNewFile(): void
    {
        $fileName = $this->getTmpFileName();
        unlink($fileName);

        $generator = $this->getGenerator($fileName);

        $generator->generateRoute(
            $fileName,
            '/V1/menuItem/:menuItemId',
            'GET',
            'Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface',
            'getById',
            ['Vendor1_Module1::menu_item']
        );

        $this->assertEquals($this->getExpectedNewContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    public function testGenerateRouteForExistingFile(): void
    {
        $fileName = $this->getTmpFileName();
        file_put_contents($fileName, $this->getExistingContent());

        $generator = $this->getGenerator($fileName);

        $generator->generateRoute(
            $fileName,
            '/V1/menuItem/:menuItemId',
            'GET',
            'Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface',
            'getById',
            ['Vendor1_Module1::menu_item']
        );

        $this->assertEquals($this->getExpectedExistingContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    protected function getGenerator(string $fileName): WebapiXmlGenerator
    {
        $source = new XmlSource($fileName);

        /** @var MockObject|SourceFactory $sourceFactory */
        $sourceFactory = $this->createMock(SourceFactory::class);
        $sourceFactory->expects($this->once())
            ->method('create')
            ->with($fileName, 'xml')
            ->willReturn($source);

        return new WebapiXmlGenerator($sourceFactory);
    }

    protected function getTmpFileName(): string
    {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    protected function getExpectedNewContent(): string
    {
        return '<?xml version="1.0"?>
<routes xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Webapi:etc/webapi.xsd">
    <route url="/V1/menuItem/:menuItemId" method="GET">
        <service class="Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface" method="getById"/>
        <resources>
            <resource ref="Vendor1_Module1::menu_item"/>
        </resources>
    </route>
</routes>
';
    }

    protected function getExistingContent(): string
    {
        return '<?xml version="1.0"?>
<routes xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Webapi:etc/webapi.xsd">
    <route url="/V1/menuItem/:menuItemId" method="GET">
        <testNode1/>
    </route>
    <route url="/V1/menu/:menuId" method="GET">
        <testNode2/>
    </route>
</routes>
';
    }

    protected function getExpectedExistingContent(): string
    {
        return '<?xml version="1.0"?>
<routes xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Webapi:etc/webapi.xsd">
    <route url="/V1/menuItem/:menuItemId" method="GET">
        <testNode1/>
    </route>
    <route url="/V1/menu/:menuId" method="GET">
        <testNode2/>
    </route>
</routes>
';
    }
}
