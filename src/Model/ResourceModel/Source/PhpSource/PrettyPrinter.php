<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\ResourceModel\Source\PhpSource;

use PhpParser\PrettyPrinter\Standard;

class PrettyPrinter extends Standard
{
    public function prettyPrintFile(array $stmts) : string
    {
        $result = parent::prettyPrintFile($stmts);

        $result = str_replace([') : ', "<?php\n\n"], ['): ', "<?php\n"], $result);
        $result = preg_replace('!([ ]+[a-z0-9}]+[^\n]*\n)([ ]+[/a-z0-9]+)!i', "\$1\n\$2", $result);
        $result = preg_replace('!(\nnamespace)!i', "\n\$1", $result);

        return preg_replace('!(\nuse [^\n]+\n)(/|class|interface)!i', "\$1\n\$2", $result);
    }
}
