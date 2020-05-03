<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator;

use IgorRain\CodeGenerator\Model\Context\ModuleContext;
use IgorRain\CodeGenerator\Model\Generator\ComposerJsonGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\JsonSource;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ComposerJsonGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $fileName = $this->getTmpFileName();

        $source = new JsonSource($fileName);

        /** @var MockObject|SourceFactory $sourceFactory */
        $sourceFactory = $this->createMock(SourceFactory::class);
        $sourceFactory->expects($this->once())
            ->method('create')
            ->with($fileName, 'json')
            ->willReturn($source);

        $generator = new ComposerJsonGenerator($sourceFactory);
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
        return '{
    "name": "vendor1/module-module1",
    "description": "N/A",
    "require": {
        "php": "~7.1.3||~7.2.0||~7.3.0",
        "vendor1/module-module2": "~0.0.1",
        "vendor1/module-module3": "~0.0.1"
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
            "Vendor1\\\\Module1\\\\": ""
        }
    },
    "version": "0.0.1"
}
';
    }
}
