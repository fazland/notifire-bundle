<?php

namespace Fazland\NotifireBundle\VariableRenderer;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class MailgunRenderer implements VariableRendererInterface
{
    const PREFIX = "recipient.";

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "mailgun";
    }

    /**
     * {@inheritdoc}
     */
    public function render($variableName)
    {
        return "%" . static::PREFIX . $variableName . "%";
    }
}
