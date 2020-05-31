<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource
 */
class TextSourceTest extends TestCase
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var TextSource
     */
    private $source;

    public function setUp(): void
    {
        $this->fileName = tempnam(sys_get_temp_dir(), 'test');
        $this->source = new TextSource($this->fileName);
    }

    public function tearDown(): void
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
    }

    public function testLoadExistingFileGetContent(): void
    {
        file_put_contents($this->fileName, $this->getSampleText());
        $this->source->load();
        $this->assertEquals($this->getSampleText(), $this->source->getContent());
    }

    public function testSetContentSave(): void
    {
        $this->source->setContent($this->getSampleText());
        $this->source->save();
        $this->assertEquals($this->getSampleText(), file_get_contents($this->fileName));
    }

    public function testLoadSave(): void
    {
        file_put_contents($this->fileName, $this->getSampleText());
        $this->source->load();
        $this->source->save();
        $this->assertEquals($this->getSampleText(), file_get_contents($this->fileName));
    }

    protected function getSampleText(): string
    {
        return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean id mi eros. Ut imperdiet gravida odio.
        Suspendisse faucibus dignissim justo et sodales. Mauris non suscipit nisi. Morbi vestibulum purus leo, at feugiat
        massa laoreet nec. Morbi bibendum sodales enim, et porta tellus laoreet eget. Sed id purus at quam gravida facilisis.
        Nulla imperdiet pellentesque velit bibendum condimentum. Vestibulum sed tellus eu ipsum semper hendrerit maximus ac diam.';
    }
}
