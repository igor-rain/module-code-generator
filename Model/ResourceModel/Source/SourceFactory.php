<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

use Magento\Framework\ObjectManagerInterface;

class SourceFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var array
     */
    private $sources;

    public function __construct(
        ObjectManagerInterface $objectManager,
        $sources = []
    ) {
        $this->objectManager = $objectManager;
        $this->sources = $sources;
    }

    /**
     * @param string $fileName
     * @param string $sourceType
     *
     * @return SourceInterface
     */
    public function create($fileName, $sourceType): SourceInterface
    {
        if (!isset($this->sources[$sourceType])) {
            throw new \RuntimeException(sprintf('Invalid source type %s', $sourceType));
        }

        return $this->objectManager->create($this->sources[$sourceType], [
            'fileName' => $fileName,
        ]);
    }
}
