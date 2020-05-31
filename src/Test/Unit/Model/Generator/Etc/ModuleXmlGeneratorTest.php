<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Etc;

use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use IgorRain\CodeGenerator\Model\Generator\Etc\ModuleXmlGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Generator\Etc\ModuleXmlGenerator
 */
class ModuleXmlGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $fileName = $this->getTmpFileName();

        $source = new XmlSource($fileName);

        /** @var MockObject|SourceFactory $sourceFactory */
        $sourceFactory = $this->createMock(SourceFactory::class);
        $sourceFactory->expects($this->once())
            ->method('create')
            ->with($fileName, 'xml')
            ->willReturn($source);

        $generator = new ModuleXmlGenerator($sourceFactory);
        $generator->generate($fileName, $this->getContext());

        $this->assertEquals($this->getExpectedContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    protected function getTmpFileName()
    {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    protected function getContext(): ModuleContext
    {
        $context1 = new ModuleContext('Vendor1_Module2', '/tmp/module', '0.0.1', []);
        $context2 = new ModuleContext('Vendor1_Module3', '/tmp/module', '0.0.1', []);

        return new ModuleContext('Vendor1_Module1', '/tmp/module', '0.0.1', [
            $context1,
            $context2,
        ]);
    }

    protected function getExpectedContent(): string
    {
        return '<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Vendor1_Module1" setup_version="0.0.1">
        <sequence>
            <module name="Vendor1_Module2"/>
            <module name="Vendor1_Module3"/>
        </sequence>
    </module>
</config>
';
    }
}
