<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

class TextSource implements SourceInterface
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var null|string
     */
    private $content;

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

        $this->content = file_get_contents($this->fileName);
    }

    public function save(): void
    {
        $dir = dirname($this->fileName);
        if (!is_dir($dir) && !mkdir($dir, 0770, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        file_put_contents($this->fileName, $this->content);
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
