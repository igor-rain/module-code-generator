<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\TextSource;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory
 */
class SourceFactoryTest extends TestCase
{
    public const FILE_NAME = '/test';
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function setUp(): void
    {
        $this->sourceFactory = new SourceFactory([
            'text' => TextSource::class
        ]);
    }

    public function testCreate(): void
    {
        $this->assertInstanceOf(TextSource::class, $this->sourceFactory->create(self::FILE_NAME, 'text'));
    }

    public function testCreateInvalidType(): void
    {
        $this->expectExceptionMessage('Invalid source type qqq');
        $this->sourceFactory->create(self::FILE_NAME, 'qqq');
    }
}
