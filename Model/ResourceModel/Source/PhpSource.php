<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

use PhpParser\Node;
use PhpParser\ParserFactory;

class PhpSource implements SourceInterface
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var Node[]
     */
    private $stmts = [];

    public function __construct(
        $fileName
    ) {
        $this->fileName = $fileName;
    }

    public function load()
    {
        if (!file_exists($this->fileName)) {
            throw new \RuntimeException(sprintf('Missing file %s', $this->fileName));
        }

        $content = file_get_contents($this->fileName);
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->stmts = $parser->parse($content);
    }

    public function save()
    {
        $dir = dirname($this->fileName);
        if (!is_dir($dir) && !mkdir($dir, 0770, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        $prettyPrinter = new PhpSource\PrettyPrinter();
        $content = $prettyPrinter->prettyPrintFile($this->stmts) . PHP_EOL;
        file_put_contents($this->fileName, $content);
    }

    /**
     * @return Node[]
     */
    public function getStmts()
    {
        return $this->stmts;
    }

    /**
     * @param Node[] $stmts
     */
    public function setStmts($stmts)
    {
        $this->stmts = $stmts;
    }
}
