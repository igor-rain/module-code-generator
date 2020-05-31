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
 * @covers \IgorRain\CodeGenerator\Model\Generator\Model\ModelGenerator
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

    public function getId(): ?int
    {
        return parent::getId();
    }

    public function getSku(): ?string
    {
        return $this->getData(self::SKU);
    }

    public function setSku(?string $sku): ItemInterface
    {
        return $this->setData(self::SKU, $sku);
    }

    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    public function setName(?string $name): ItemInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription(?string $description): ItemInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getPrice(): ?float
    {
        return $this->getData(self::PRICE);
    }

    public function setPrice(?float $price): ItemInterface
    {
        return $this->setData(self::PRICE, $price);
    }

    public function getAttributeSetId(): ?int
    {
        return $this->getData(self::ATTRIBUTE_SET_ID);
    }

    public function setAttributeSetId(?int $attributeSetId): ItemInterface
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    public function getIsVisible(): ?bool
    {
        return $this->getData(self::IS_VISIBLE);
    }

    public function setIsVisible(?bool $isVisible): ItemInterface
    {
        return $this->setData(self::IS_VISIBLE, $isVisible);
    }
}
';
    }
}
