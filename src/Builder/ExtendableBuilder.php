<?php

namespace Fazland\NotifireBundle\Builder;

use Fazland\Notifire\NotifireBuilder;
use Fazland\NotifireBundle\Extension\ExtensionInterface;

class ExtendableBuilder extends NotifireBuilder
{
    public function addExtension(ExtensionInterface $extension)
    {
        $extension->register($this);
    }
}
