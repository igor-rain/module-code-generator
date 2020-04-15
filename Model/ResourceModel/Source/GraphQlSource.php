<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\Parser;
use GraphQL\Language\Printer;

class GraphQlSource implements SourceInterface
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var null|DocumentNode
     */
    private $ast;

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
        $this->ast = Parser::parse($content);
    }

    public function save(): void
    {
        $dir = dirname($this->fileName);
        if (!is_dir($dir) && !mkdir($dir, 0770, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        $content = Printer::doPrint($this->getDocumentNode());
        $content = preg_replace_callback('/^( +)/m', static function ($a) {
            return str_repeat(' ', (int) (strlen($a[1]) / 2) * 4);
        }, $content);
        file_put_contents($this->fileName, $content);
    }

    public function getDocumentNode(): ?DocumentNode
    {
        if ($this->ast === null) {
            $this->ast = new DocumentNode([]);
        }
        return $this->ast;
    }
}
