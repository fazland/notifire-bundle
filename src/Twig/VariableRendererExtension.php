<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\Twig;

use Fazland\NotifireBundle\Exception\VariableRendererNotFoundException;
use Fazland\NotifireBundle\VariableRenderer\Factory;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class VariableRendererExtension extends \Twig_Extension
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var string
     */
    private $defaultRenderer;

    /**
     * VariableRendererExtension constructor.
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string $renderer
     */
    public function setDefaultRenderer($renderer)
    {
        $this->defaultRenderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('render_variable', [$this, 'render']),
        ];
    }

    /**
     * @param string $variable
     * @param string $rendererName
     *
     * @return string
     *
     * @throws VariableRendererNotFoundException
     */
    public function render(string $variable, string $rendererName = ''): string
    {
        $targetRenderer = null;

        if (empty($rendererName)) {
            $targetRenderer = $this->defaultRenderer;
        }

        if (empty($targetRenderer)) {
            throw new VariableRendererNotFoundException('No renderer specified');
        }

        return $this->factory
            ->get(empty($rendererName) ? $this->defaultRenderer : $rendererName)
            ->render($variable)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'fazland.notifire.variable_renderer_extension';
    }
}
