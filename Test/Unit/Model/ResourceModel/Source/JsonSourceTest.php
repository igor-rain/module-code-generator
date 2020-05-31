<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\JsonSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\ResourceModel\Source\JsonSource
 */
class JsonSourceTest extends TestCase
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
        $this->source = new JsonSource($this->fileName);
    }

    public function tearDown(): void
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
    }

    public function testLoadSave(): void
    {
        file_put_contents($this->fileName, $this->getSampleJson());
        $this->source->load();
        $this->source->save();
        $this->assertEquals($this->getSampleJson(), file_get_contents($this->fileName));
    }

    public function testMergeSave(): void
    {
        $this->source->merge(json_decode($this->getSampleJson(), true));
        $this->source->save();
        $this->assertEquals($this->getSampleJson(), file_get_contents($this->fileName));
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
