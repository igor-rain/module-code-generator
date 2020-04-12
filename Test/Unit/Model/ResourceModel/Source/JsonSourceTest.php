<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\JsonSource;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class JsonSourceTest extends TestCase
{
    public function testLoadMissingFile(): void
    {
        $this->expectException('RuntimeException');
        $jsonSource = new JsonSource('/tmp/missing-file');
        $jsonSource->load();
    }

    public function testLoadSave(): void
    {
        $fileName = $this->getTmpFileName();
        file_put_contents($fileName, $this->getSampleJson());

        $jsonSource = new JsonSource($fileName);
        $jsonSource->load();
        $jsonSource->save();

        $this->assertEquals($this->getSampleJson(), file_get_contents($fileName));
        unlink($fileName);
    }

    public function testMergeSave(): void
    {
        $fileName = $this->getTmpFileName();

        $jsonSource = new JsonSource($fileName);
        $jsonSource->merge(json_decode($this->getSampleJson(), true));
        $jsonSource->save();

        $this->assertEquals($this->getSampleJson(), file_get_contents($fileName));
        unlink($fileName);
    }

    protected function getTmpFileName()
    {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    protected function getSampleJson(): string
    {
        return '{
    "name": "magento/module-test",
    "description": "Magento module responsible for Testing",
    "require": {
        "php": "~7.1.3||~7.2.0||~7.3.0"
    },
    "type": "magento2-module",
    "license": [
        "OSL-3.0",
        "AFL-3.0"
    ],
    "autoload": {
        "files": [
            "registration.php"
        ],
        "psr-4": {
            "Magento\\\\Test\\\\": ""
        }
    },
    "version": "1.0.0"
}
';
    }
}
