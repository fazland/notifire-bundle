<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\VariableRenderer;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
interface VariableRendererInterface
{
    /**
     * Returns the name of the current renderer.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Given a $variableName, the renderer MUST return its string representation.
     *
     * @param string $variableName
     *
     * @return string
     */
    public function render(string $variableName): string;
}
