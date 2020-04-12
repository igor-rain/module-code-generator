<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

class JsonSource implements SourceInterface
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var array
     */
    private $data = [];

    public function __construct(
        $fileName
    ) {
        $this->fileName = $fileName;
    }

    public function exists(): bool
    {
        return file_exists($this->fileName);
    }

    public function load(): void
    {
        if (!file_exists($this->fileName)) {
            throw new \RuntimeException(sprintf('Missing file %s', $this->fileName));
        }

        $content = file_get_contents($this->fileName);
        $this->data = json_decode($content, true);
    }

    public function merge($data): void
    {
        $this->data = array_replace_recursive($this->data, $data);
    }

    public function save(): void
    {
        $content = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $content .= PHP_EOL;

        $dir = dirname($this->fileName);
        if (!is_dir($dir) && !mkdir($dir, 0770, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        file_put_contents($this->fileName, $content);
    }
}
