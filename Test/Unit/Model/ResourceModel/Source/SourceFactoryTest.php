<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\ResourceModel\Source;

use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceInterface;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class SourceFactoryTest extends TestCase
{
    /**
     * @var ObjectManagerInterface|MockObject
     */
    private $objectManager;
    /**
     * @var array
     */
    private $sources;
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function setUp()
    {
        $this->objectManager = $this->createMock(ObjectManagerInterface::class);
        $this->sources = [
            'php' => 'ClassA',
            'json' => 'ClassB',
        ];
        $this->sourceFactory = new SourceFactory($this->objectManager, $this->sources);
    }

    public function getCreateData(): array
    {
        return [
            ['/tmp/aaa.php', 'php'],
            ['/tmp/bbb.json', 'json'],
        ];
    }

    /**
     * @dataProvider getCreateData
     *
     * @param $fileName
     * @param $sourceType
     */
    public function testCreate($fileName, $sourceType): void
    {
        $source = $this->createMock(SourceInterface::class);

        $this->objectManager
            ->expects($this->once())
            ->method('create')
            ->with($this->sources[$sourceType], [
                'fileName' => $fileName,
            ])
            ->willReturn($source);

        $this->assertSame($source, $this->sourceFactory->create($fileName, $sourceType));
    }

    public function testCreateWithWrongSourceType(): void
    {
        $this->expectExceptionMessage('Invalid source type qqq');
        $this->sourceFactory->create('/tmp/a.qqq', 'qqq');
    }
}
