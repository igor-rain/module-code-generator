<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Etc;

use IgorRain\CodeGenerator\Model\Generator\Etc\DiXmlGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DiXmlGeneratorTest extends TestCase
{
    public function testGeneratePreferenceForNewFile(): void
    {
        $fileName = $this->getTmpFileName();
        unlink($fileName);

        $generator = $this->getGenerator($fileName);

        $context = ModelContextTest::createContext();
        $generator->generatePreference($fileName, $context->getRepositoryInterface()->getName(), $context->getRepository()->getName());

        $this->assertEquals($this->getExpectedNewContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    public function testGeneratePreferenceForExistingFile(): void
    {
        $fileName = $this->getTmpFileName();
        file_put_contents($fileName, $this->getExistingContent());

        $generator = $this->getGenerator($fileName);

        $context = ModelContextTest::createContext();
        $generator->generatePreference($fileName, $context->getRepositoryInterface()->getName(), $context->getRepository()->getName());

        $this->assertEquals($this->getExpectedExistingContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    /**
     * @param $fileName
     *
     * @return DiXmlGenerator
     */
    protected function getGenerator($fileName): DiXmlGenerator
    {
        $source = new XmlSource($fileName);

        /** @var MockObject|SourceFactory $sourceFactory */
        $sourceFactory = $this->createMock(SourceFactory::class);
        $sourceFactory->expects($this->once())
            ->method('create')
            ->with($fileName, 'xml')
            ->willReturn($source);

        return new DiXmlGenerator($sourceFactory);
    }

    protected function getTmpFileName()
    {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    protected function getExpectedNewContent(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <preference for="Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface" type="Vendor1\Module1\Model\Menu\ItemRepository"/>
</config>
';
    }

    protected function getExistingContent(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <preference for="Vendor1\Module1Api\Api\Data\Menu\ItemInterface" type="Vendor1\Module1\Model\Menu\Item"/>
    <preference for="Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface" type="Vendor1\Module1\Model\NotExistingClass"/>
</config>
';
    }

    protected function getExpectedExistingContent(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <preference for="Vendor1\Module1Api\Api\Data\Menu\ItemInterface" type="Vendor1\Module1\Model\Menu\Item"/>
    <preference for="Vendor1\Module1Api\Api\Menu\ItemRepositoryInterface" type="Vendor1\Module1\Model\Menu\ItemRepository"/>
</config>
';
    }
}
