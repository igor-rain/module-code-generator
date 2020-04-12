<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class XmlSourceTest extends TestCase
{
    public function testLoadMissingFile(): void
    {
        $this->expectException('RuntimeException');
        $xmlSource = new XmlSource('/tmp/missing-file');
        $xmlSource->load();
    }

    public function testLoadSave(): void
    {
        $fileName = $this->getTmpFileName();
        file_put_contents($fileName, $this->getSampleXml());

        $xmlSource = new XmlSource($fileName);
        $xmlSource->load();
        $xmlSource->save();

        $this->assertEquals($this->getSampleXml(), file_get_contents($fileName));
        unlink($fileName);
    }

    protected function getTmpFileName()
    {
        return tempnam(sys_get_temp_dir(), 'test');
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
