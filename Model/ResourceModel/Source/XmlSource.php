<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

use DOMDocument;

class XmlSource extends AbstractSource
{
    /**
     * @var DOMDocument
     */
    private $dom;

    public function __construct(
        $fileName
    ) {
        parent::__construct($fileName);
        $this->dom = new DOMDocument('1.0');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;
    }

    public function getDocument(): DOMDocument
    {
        return $this->dom;
    }

    protected function getContent(): ?string
    {
        $content = $this->dom->saveXML();
        return preg_replace_callback('/^( +)</m', static function ($a) {
            return str_repeat(' ', (int) (strlen($a[1]) / 2) * 4) . '<';
        }, $content);
    }

    protected function setContent(string $content): void
    {
        $this->dom->loadXML($content);
    }
}
