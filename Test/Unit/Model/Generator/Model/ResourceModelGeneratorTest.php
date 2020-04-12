<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Model;

use IgorRain\CodeGenerator\Model\Generator\Model\ResourceModelGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Generator\AbstractPhpSourceGeneratorTest;

/**
 * @internal
 * @coversNothing
 */
class ResourceModelGeneratorTest extends AbstractPhpSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new ResourceModelGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1\Model\ResourceModel\Menu;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\PredefinedId;
use Vendor1\Module1Api\Api\Data\Menu\ItemInterface;

/**
 * @method Item load(ItemInterface $object, $value, $field=null)
 * @method Item delete(ItemInterface $object)
 * @method Item save(ItemInterface $object)
 */
class Item extends AbstractDb
{
    /**
     * Provides possibility of saving entity with predefined/pre-generated id
     */
    use PredefinedId;

    protected function _construct()
    {
        $this->_init(\'menu_item_entity\', \'entity_id\');
    }
}
';
    }
}
