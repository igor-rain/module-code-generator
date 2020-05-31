<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

use Magento\Framework\Component\ComponentRegistrar;

if (class_exists(ComponentRegistrar::class)) {
    ComponentRegistrar::register(
        ComponentRegistrar::MODULE,
        'IgorRain_CodeGenerator',
        __DIR__
    );
}
