<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Test\Unit\Model\Make;

use IgorRain\CodeGenerator\Model\Make\Module;
use IgorRain\CodeGenerator\Test\Unit\Model\Context\ModuleContextTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IgorRain\CodeGenerator\Model\Make\Module
 */
class ModuleTest extends TestCase
{
    /**
     * @var MockObject[]
     */
    private $parameters = [];
    /**
     * @var Module
     */
    private $makeModule;
    /**
     * @var array
     */
    private $checkedGenerators = [];

    public function setUp(): void
    {
        $class = new \ReflectionClass(Module::class);
        $constructor = $class->getConstructor();
        foreach ($constructor->getParameters() as $parameter) {
            $this->parameters[$parameter->getName()] = $this->createMock($parameter->getType()->getName());
        }
        $this->makeModule = $class->newInstanceArgs($this->parameters);
    }

    public function testMake(): void
    {
        $context = ModuleContextTest::createContext();

        $this->expectedCallGenerateOnce('composerJsonGenerator', [
            '/tmp/module/composer.json',
            $context
        ]);
        $this->expectedCallGenerateOnce('registrationPhpGenerator', [
            '/tmp/module/registration.php',
            $context
        ]);
        $this->expectedCallGenerateOnce('moduleXmlGenerator', [
            '/tmp/module/etc/module.xml',
            $context
        ]);

        $this->makeModule->make($context);
        $this->assertSameSize($this->parameters, $this->checkedGenerators);
    }

    protected function expectedCallGenerateOnce($paramName, $data): void
    {
        $this->expectedCallOnce($paramName, 'generate', $data);
    }

    protected function expectedCallOnce($paramName, $method, $data): void
    {
        if (isset($this->checkedGenerators[$paramName])) {
            throw new \RuntimeException('Generator ' . $paramName . ' was already checked');
        }

        $this->parameters[$paramName]
            ->expects($this->once())
            ->method($method)
            ->with(...$data);
        $this->checkedGenerators[$paramName] = true;
    }
}
