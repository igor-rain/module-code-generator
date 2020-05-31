<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

class JsonSource extends AbstractSource
{
    /**
     * @var array
     */
    private $data = [];

    public function merge($data): void
    {
        $this->data = array_replace_recursive($this->data, $data);
    }

    protected function getContent(): ?string
    {
        return json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    }

    protected function setContent(string $content): void
    {
        $this->data = json_decode($content, true);
    }
}
