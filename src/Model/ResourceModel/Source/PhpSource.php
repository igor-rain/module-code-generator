<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

use PhpParser\Node;
use PhpParser\ParserFactory;

class PhpSource extends AbstractSource
{
    /**
     * @var Node[]
     */
    private $stmts = [];

    /**
     * @return Node[]
     */
    public function getStmts(): array
    {
        return $this->stmts;
    }

    /**
     * @param Node[] $stmts
     */
    public function setStmts(array $stmts): void
    {
        $this->stmts = $stmts;
    }

    protected function getContent(): ?string
    {
        $prettyPrinter = new PhpSource\PrettyPrinter();
        return $prettyPrinter->prettyPrintFile($this->stmts) . PHP_EOL;
    }

    protected function setContent(string $content): void
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->stmts = $parser->parse($content);
    }
}
