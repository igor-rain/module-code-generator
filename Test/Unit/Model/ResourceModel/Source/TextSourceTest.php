<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class TextSourceTest extends TestCase
{
    public function testSetContentSave(): void
    {
        $fileName = $this->getTmpFileName();
        $textSource = new TextSource($fileName);
        $textSource->setContent($this->getSampleText());
        $textSource->save();

        $this->assertEquals($this->getSampleText(), file_get_contents($fileName));
        unlink($fileName);
    }

    public function testLoadSave(): void
    {
        $fileName = $this->getTmpFileName();
        file_put_contents($fileName, $this->getSampleText());

        $textSource = new TextSource($fileName);
        $textSource->load();
        $textSource->save();

        $this->assertEquals($this->getSampleText(), file_get_contents($fileName));
        unlink($fileName);
    }

    protected function getTmpFileName()
    {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    protected function getSampleText(): string
    {
        return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean id mi eros. Ut imperdiet gravida odio.
        Suspendisse faucibus dignissim justo et sodales. Mauris non suscipit nisi. Morbi vestibulum purus leo, at feugiat
        massa laoreet nec. Morbi bibendum sodales enim, et porta tellus laoreet eget. Sed id purus at quam gravida facilisis.
        Nulla imperdiet pellentesque velit bibendum condimentum. Vestibulum sed tellus eu ipsum semper hendrerit maximus ac diam.';
    }
}
