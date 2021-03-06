<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\VariableRenderer;

use Fazland\NotifireBundle\Exception\VariableRendererAlreadyRegisteredException;
use Fazland\NotifireBundle\Exception\VariableRendererNotFoundException;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class Factory
{
    /**
     * @var VariableRendererInterface[]
     */
    private $renderers;

    public function __construct()
    {
        $this->renderers = [];
    }

    /**
     * @param VariableRendererInterface $renderer
     *
     * @throws VariableRendererAlreadyRegisteredException
     */
    public function addRenderer(VariableRendererInterface $renderer): void
    {
        $name = $renderer->getName();
        if (isset($this->renderers[$name])) {
            throw new VariableRendererAlreadyRegisteredException("A renderer with name '$name' has been already registered");
        }

        $this->renderers[$name] = $renderer;
    }

    /**
     * @param string $name
     *
     * @return VariableRendererInterface
     *
     * @throws VariableRendererNotFoundException
     */
    public function get(string $name): VariableRendererInterface
    {
        if (! isset($this->renderers[$name])) {
            throw new VariableRendererNotFoundException("Could not find a renderer with name '$name'");
        }

        return $this->renderers[$name];
    }
}
