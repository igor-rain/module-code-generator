<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model;

use IgorRain\CodeGenerator\Model\Locator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class LocatorTest extends TestCase
{
    public function testGetNewModulePath(): void
    {
        $locator = new Locator();
        $modulePath = $locator->getNewModulePath('Vendor1_Module1');
        $this->assertEquals($modulePath, BP . '/app/code/Vendor1/Module1');
    }

    public function testGetNewModulePathUsingWrongModuleName(): void
    {
        $this->expectExceptionMessage('Invalid module name A_B_C');
        $locator = new Locator();
        $locator->getNewModulePath('A_B_C');
    }
}
