<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

class SourceFactory
{
    /**
     * @var array
     */
    private $sources;

    public function __construct(
        $sources = []
    ) {
        $this->sources = $sources;
    }

    public function create(string $fileName, string $sourceType): SourceInterface
    {
        if (!isset($this->sources[$sourceType])) {
            throw new \RuntimeException(sprintf('Invalid source type %s', $sourceType));
        }

        $className = $this->sources[$sourceType];
        return new $className($fileName);
    }
}
