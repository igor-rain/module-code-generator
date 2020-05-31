<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Etc;

use IgorRain\CodeGenerator\Model\Generator\Etc\AclXmlGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Generator\Etc\AclXmlGenerator
 */
class AclXmlGeneratorTest extends TestCase
{
    public function testGenerateModelResource(): void
    {
        $fileName = '/tmp/filename';
        $context = ModelContextTest::createContext();

        /** @var AclXmlGenerator|MockObject $generator */
        $generator = $this->createPartialMock(AclXmlGenerator::class, [
            'generateResource'
        ]);

        $generator->expects($this->once())
            ->method('generateResource')
            ->with($fileName, 'Vendor1_Module1::menu_item', 'Magento_Backend::admin', [
                'title' => 'Menu item',
                'translate' => 'title',
                'sortOrder' => 10
            ]);

        $generator->generateModelResource($fileName, $context);
    }

    public function testGenerateResourceForNewFile(): void
    {
        $fileName = $this->getTmpFileName();
        unlink($fileName);

        $generator = $this->getGenerator($fileName);

        $generator->generateResource($fileName, 'Vendor1_Module1::menu_item', 'Magento_Backend::admin', [
            'title' => 'Menu item',
            'translate' => 'title',
            'sortOrder' => 10
        ]);

        $this->assertEquals($this->getExpectedNewContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    public function testGenerateResourceForExistingFile(): void
    {
        $fileName = $this->getTmpFileName();
        file_put_contents($fileName, $this->getExistingContent());

        $generator = $this->getGenerator($fileName);

        $generator->generateResource($fileName, 'Vendor1_Module1::menu_item', 'Magento_Backend::admin', [
            'title' => 'Menu item',
            'translate' => 'title',
            'sortOrder' => 10
        ]);

        $this->assertEquals($this->getExpectedExistingContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    protected function getGenerator(string $fileName): AclXmlGenerator
    {
        $source = new XmlSource($fileName);

        /** @var MockObject|SourceFactory $sourceFactory */
        $sourceFactory = $this->createMock(SourceFactory::class);
        $sourceFactory->expects($this->once())
            ->method('create')
            ->with($fileName, 'xml')
            ->willReturn($source);

        return new AclXmlGenerator($sourceFactory);
    }

    protected function getTmpFileName(): string
    {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    protected function getExpectedNewContent(): string
    {
        return '<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Acl/etc/acl.xsd">
    <acl>
        <resources>
            <resource id="Magento_Backend::admin">
                <resource id="Vendor1_Module1::menu_item" title="Menu item" translate="title" sortOrder="10"/>
            </resource>
        </resources>
    </acl>
</config>
';
    }

    protected function getExistingContent(): string
    {
        return '<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Acl/etc/acl.xsd">
    <acl>
        <resources>
            <resource id="Magento_Backend::test"/>
        </resources>
    </acl>
</config>
';
    }

    protected function getExpectedExistingContent(): string
    {
        return '<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Acl/etc/acl.xsd">
    <acl>
        <resources>
            <resource id="Magento_Backend::test"/>
            <resource id="Magento_Backend::admin">
                <resource id="Vendor1_Module1::menu_item" title="Menu item" translate="title" sortOrder="10"/>
            </resource>
        </resources>
    </acl>
</config>
';
    }
}
