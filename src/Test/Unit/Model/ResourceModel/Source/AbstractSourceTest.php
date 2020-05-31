<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\AbstractSource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\ResourceModel\Source\AbstractSource
 */
class AbstractSourceTest extends TestCase
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var AbstractSource|MockObject
     */
    private $source;

    public function setUp(): void
    {
        $this->fileName = tempnam(sys_get_temp_dir(), 'test');
        $this->source = $this->getMockBuilder(AbstractSource::class)
            ->setConstructorArgs([$this->fileName])
            ->setMethods([
                'getContent',
                'setContent'
            ])
            ->getMock();
    }

    public function tearDown(): void
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
    }

    /**
     * @param bool $exists
     * @testWith [true]
     *           [false]
     */
    public function testExists(bool $exists): void
    {
        if (!$exists) {
            unlink($this->fileName);
        }
        $this->assertEquals($exists, $this->source->exists());
    }

    public function testLoadMissingFile(): void
    {
        unlink($this->fileName);
        $this->expectExceptionMessage('Missing file ' . $this->fileName);
        $this->source->load();
    }

    public function testLoadExistingFile(): void
    {
        $text = $this->getSampleText();

        $this->source
            ->expects($this->once())
            ->method('setContent')
            ->with($text);

        file_put_contents($this->fileName, $text);
        $this->source->load();
    }

    public function testSave(): void
    {
        $text = $this->getSampleText();

        $this->source
            ->expects($this->once())
            ->method('getContent')
            ->with()
            ->willReturn($text);

        $this->source->save();
        $this->assertEquals($text, file_get_contents($this->fileName));
    }

    public function testSaveWithNullContent(): void
    {
        $this->source
            ->expects($this->once())
            ->method('getContent')
            ->with()
            ->willReturn(null);

        $this->expectExceptionMessage('Content wasn\'t initialized');
        $this->source->save();
    }

    protected function getSampleText(): string
    {
        return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean id mi eros. Ut imperdiet gravida odio.
        Suspendisse faucibus dignissim justo et sodales. Mauris non suscipit nisi. Morbi vestibulum purus leo, at feugiat
        massa laoreet nec. Morbi bibendum sodales enim, et porta tellus laoreet eget. Sed id purus at quam gravida facilisis.
        Nulla imperdiet pellentesque velit bibendum condimentum. Vestibulum sed tellus eu ipsum semper hendrerit maximus ac diam.';
    }
}
