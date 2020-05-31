<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

abstract class AbstractSource implements SourceInterface
{
    /**
     * @var string
     */
    protected $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function exists(): bool
    {
        return file_exists($this->fileName);
    }

    public function load(): void
    {
        if (!$this->exists()) {
            throw new \RuntimeException(sprintf('Missing file %s', $this->fileName));
        }

        $this->setContent(file_get_contents($this->fileName));
    }

    public function save(): void
    {
        $content = $this->getContent();
        if ($content === null) {
            throw new \RuntimeException('Content wasn\'t initialized');
        }

        $dir = dirname($this->fileName);
        if (!is_dir($dir) && !@mkdir($dir, 0770, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        file_put_contents($this->fileName, $content);
    }

    abstract protected function getContent(): ?string;

    abstract protected function setContent(string $content): void;
}
