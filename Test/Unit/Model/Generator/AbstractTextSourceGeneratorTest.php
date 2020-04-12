<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
abstract class AbstractTextSourceGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $fileName = $this->getTmpFileName();

        $source = new TextSource($fileName);

        /** @var MockObject|SourceFactory $sourceFactory */
        $sourceFactory = $this->createMock(SourceFactory::class);
        $sourceFactory->expects($this->once())
            ->method('create')
            ->with($fileName, 'text')
            ->willReturn($source);

        $this->generate($sourceFactory, $fileName);

        $this->assertEquals($this->getExpectedContent(), file_get_contents($fileName));

        unlink($fileName);
    }

    protected function getTmpFileName(): string
    {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    abstract protected function generate(SourceFactory $sourceFactory, string $fileName): void;

    abstract protected function getExpectedContent(): string;
}
