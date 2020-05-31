<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\Parser;
use GraphQL\Language\Printer;

class GraphQlSource extends AbstractSource
{
    /**
     * @var null|DocumentNode
     */
    private $ast;

    public function getDocumentNode(): ?DocumentNode
    {
        if ($this->ast === null) {
            $this->ast = new DocumentNode([]);
        }
        return $this->ast;
    }

    protected function getContent(): ?string
    {
        $content = Printer::doPrint($this->getDocumentNode());
        return preg_replace_callback('/^( +)/m', static function ($a) {
            return str_repeat(' ', (int) (strlen($a[1]) / 2) * 4);
        }, $content);
    }

    protected function setContent(string $content): void
    {
        $this->ast = Parser::parse($content);
    }
}
