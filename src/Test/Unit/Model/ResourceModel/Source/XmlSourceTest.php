<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource
 */
class XmlSourceTest extends TestCase
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
        $this->source = new XmlSource($this->fileName);
    }

    public function tearDown(): void
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
    }

    public function testLoadSave(): void
    {
        file_put_contents($this->fileName, $this->getSampleXml());
        $this->source->load();
        $this->source->save();
        $this->assertEquals($this->getSampleXml(), file_get_contents($this->fileName));
    }

    public function testGetDocument(): void
    {
        file_put_contents($this->fileName, $this->getSampleXml());
        $this->source->load();
        $this->assertEquals(4, $this->source->getDocument()->getElementsByTagName('module')->length);
    }

    protected function getSampleXml(): string
    {
        return '<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Magento_Test" setup_version="0.0.1">
        <sequence>
            <module name="Magento_Module1"/>
            <module name="Magento_Module2"/>
            <module name="Magento_Module3"/>
        </sequence>
    </module>
</config>
';
    }
}
