<?php

namespace Fazland\NotifireBundle\Builder;

use Fazland\NotifireBundle\Extension\ExtensionInterface;
use Fazland\Notifire\NotifireBuilder;

class ExtendableBuilder extends NotifireBuilder
{
    public function addExtension(ExtensionInterface $extension)
    {
        $extension->register($this);
    }
}
