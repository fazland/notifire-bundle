<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\VariableRenderer;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class MailgunRenderer implements VariableRendererInterface
{
    public const PREFIX = 'recipient.';

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'mailgun';
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $variableName): string
    {
        return '%'.static::PREFIX.$variableName.'%';
    }
}
