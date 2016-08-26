<?php

namespace Fazland\NotifireBundle\Tests\VariableRenderer;

use Fazland\NotifireBundle\VariableRenderer\Factory;
use Fazland\NotifireBundle\VariableRenderer\VariableRendererInterface;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new Factory();
    }

    /**
     * @expectedException \Fazland\NotifireBundle\Exception\VariableRendererNotFoundException
     */
    public function testVariableRendererNotFound()
    {
        $this->factory->get('non_existing_renderer');
    }

    /**
     * @expectedException \Fazland\NotifireBundle\Exception\VariableRendererAlreadyRegistered
     */
    public function testVariableRendererAlreadyRegistered()
    {
        /** @var VariableRendererInterface $renderer */
        $renderer = $this->prophesize(VariableRendererInterface::class);
        $renderer->getName()->willReturn('test_renderer');

        $this->factory->addRenderer($renderer->reveal());
        $this->factory->addRenderer($renderer->reveal());
    }

    public function testAddAndGetRenderer()
    {
        /** @var VariableRendererInterface $renderer */
        $renderer = $this->prophesize(VariableRendererInterface::class);
        $renderer->getName()->willReturn('test_renderer');

        $this->factory->addRenderer($renderer->reveal());

        $this->assertEquals($this->factory->get('test_renderer'), $renderer->reveal());
    }
}
