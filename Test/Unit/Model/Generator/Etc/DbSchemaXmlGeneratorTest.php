<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Etc;

use IgorRain\CodeGenerator\Model\Generator\Etc\DbSchemaXmlGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DbSchemaXmlGeneratorTest extends TestCase
{
    public function testGenerateTableForNewFile(): void
    {
        $fileName = $this->getTmpFileName();
        unlink($fileName);

        $generator = $this->getGenerator($fileName);

        $context = ModelContextTest::createContext();
        $generator->generateTable($fileName, $context);

        $this->assertEquals($this->getExpectedNewContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    public function testGenerateTableForExistingFile(): void
    {
        $fileName = $this->getTmpFileName();
        file_put_contents($fileName, $this->getExistingContent());

        $generator = $this->getGenerator($fileName);

        $context = ModelContextTest::createContext();
        $generator->generateTable($fileName, $context);

        $this->assertEquals($this->getExpectedExistingContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    /**
     * @param $fileName
     *
     * @return DbSchemaXmlGenerator
     */
    protected function getGenerator($fileName): DbSchemaXmlGenerator
    {
        $source = new XmlSource($fileName);

        /** @var MockObject|SourceFactory $sourceFactory */
        $sourceFactory = $this->createMock(SourceFactory::class);
        $sourceFactory->expects($this->once())
            ->method('create')
            ->with($fileName, 'xml')
            ->willReturn($source);

        return new DbSchemaXmlGenerator($sourceFactory);
    }

    protected function getTmpFileName()
    {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    protected function getExpectedNewContent(): string
    {
        return '<?xml version="1.0"?>
<schema xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd">
    <table name="menu_item_entity" resource="default" engine="innodb" comment="Menu Item Table">
        <column xsi:type="int" name="entity_id" padding="10" unsigned="true" nullable="false" identity="true" comment="Entity ID"/>
        <column xsi:type="varchar" name="sku" nullable="true" length="255" comment="Sku"/>
        <column xsi:type="varchar" name="name" nullable="true" length="255" comment="Name"/>
        <column xsi:type="varchar" name="price" nullable="true" length="255" comment="Price"/>
        <column xsi:type="varchar" name="attribute_set_id" nullable="true" length="255" comment="Attribute Set ID"/>
        <constraint xsi:type="primary" referenceId="PRIMARY">
            <column name="entity_id"/>
        </constraint>
    </table>
</schema>
';
    }

    protected function getExistingContent(): string
    {
        return '<?xml version="1.0"?>
<schema xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd">
    <table name="menu_item_entity" resource="default" engine="innodb" comment="Menu Item Table">
        <column xsi:type="varchar" name="sku"/>
        <column xsi:type="varchar" name="price" comment="Price"/>
        <column xsi:type="varchar" name="attribute_set_id_new" nullable="true" length="255" comment="Attribute Set ID"/>
        <constraint xsi:type="primary" referenceId="PRIMARY">
            <column name="entity_id"/>
        </constraint>
    </table>
</schema>
';
    }

    protected function getExpectedExistingContent(): string
    {
        return '<?xml version="1.0"?>
<schema xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd">
    <table name="menu_item_entity" resource="default" engine="innodb" comment="Menu Item Table">
        <column xsi:type="int" name="entity_id" padding="10" unsigned="true" nullable="false" identity="true" comment="Entity ID"/>
        <column xsi:type="varchar" name="sku"/>
        <column xsi:type="varchar" name="price" comment="Price"/>
        <column xsi:type="varchar" name="attribute_set_id_new" nullable="true" length="255" comment="Attribute Set ID"/>
        <column xsi:type="varchar" name="name" nullable="true" length="255" comment="Name"/>
        <column xsi:type="varchar" name="attribute_set_id" nullable="true" length="255" comment="Attribute Set ID"/>
        <constraint xsi:type="primary" referenceId="PRIMARY">
            <column name="entity_id"/>
        </constraint>
    </table>
</schema>
';
    }
}
