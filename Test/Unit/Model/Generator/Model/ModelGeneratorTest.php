<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Generator\Model;

use IgorRain\CodeGenerator\Model\Generator\Model\ModelGenerator;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModelContextTest;
use IgorRain\CodeGenerator\Test\Unit\Model\Generator\AbstractPhpSourceGeneratorTest;

/**
 * @internal
 * @coversNothing
 */
class ModelGeneratorTest extends AbstractPhpSourceGeneratorTest
{
    protected function generate(SourceFactory $sourceFactory, string $fileName): void
    {
        $context = ModelContextTest::createContext();
        $generator = new ModelGenerator($sourceFactory);
        $generator->generate($fileName, $context);
    }

    protected function getExpectedContent(): string
    {
        return '<?php

namespace Vendor1\Module1\Model\Menu;

use Magento\Framework\Model\AbstractModel;
use Vendor1\Module1\Model\ResourceModel\Menu\Item as ItemResource;
use Vendor1\Module1Api\Api\Data\Menu\ItemInterface;

class Item extends AbstractModel implements ItemInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = \'module1_menu_item\';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject = \'menu_item\';

    protected function _construct()
    {
        $this->_init(ItemResource::class);
    }

    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    public function getAttributeSetId()
    {
        return $this->getData(self::ATTRIBUTE_SET_ID);
    }

    public function setAttributeSetId($attributeSetId)
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }
}
';
    }
}
