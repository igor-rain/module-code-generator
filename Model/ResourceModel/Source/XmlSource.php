<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

use DOMDocument;

class XmlSource implements SourceInterface
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var DOMDocument
     */
    private $dom;

    public function __construct(
        $fileName
    ) {
        $this->fileName = $fileName;
        $this->dom = new DOMDocument('1.0');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;
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

        $this->dom->load($this->fileName);
    }

    public function save(): void
    {
        $dir = dirname($this->fileName);
        if (!is_dir($dir) && !mkdir($dir, 0770, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        $content = $this->dom->saveXML();
        $content = preg_replace_callback('/^( +)</m', static function ($a) {
            return str_repeat(' ', (int) (strlen($a[1]) / 2) * 4) . '<';
        }, $content);

        file_put_contents($this->fileName, $content);
    }

    public function getDocument(): DOMDocument
    {
        return $this->dom;
    }
}
