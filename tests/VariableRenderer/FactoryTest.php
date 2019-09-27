<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\Tests\VariableRenderer;

use Fazland\NotifireBundle\Exception\VariableRendererAlreadyRegisteredException;
use Fazland\NotifireBundle\Exception\VariableRendererNotFoundException;
use Fazland\NotifireBundle\VariableRenderer\Factory;
use Fazland\NotifireBundle\VariableRenderer\VariableRendererInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class FactoryTest extends TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->factory = new Factory();
    }

    public function testVariableRendererNotFound(): void
    {
        $this->expectException(VariableRendererNotFoundException::class);

        $this->factory->get('non_existing_renderer');
    }

    public function testVariableRendererAlreadyRegistered(): void
    {
        $this->expectException(VariableRendererAlreadyRegisteredException::class);

        /** @var VariableRendererInterface $renderer */
        $renderer = $this->prophesize(VariableRendererInterface::class);
        $renderer->getName()->willReturn('test_renderer');

        $this->factory->addRenderer($renderer->reveal());
        $this->factory->addRenderer($renderer->reveal());
    }

    public function testAddAndGetRenderer(): void
    {
        /** @var VariableRendererInterface $renderer */
        $renderer = $this->prophesize(VariableRendererInterface::class);
        $renderer->getName()->willReturn('test_renderer');

        $this->factory->addRenderer($renderer->reveal());

        self::assertEquals($this->factory->get('test_renderer'), $renderer->reveal());
    }
}
