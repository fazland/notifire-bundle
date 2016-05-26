<?php

namespace Fazland\NotifierBundle\Builder;

use Fazland\NotifierBundle\Extension\ExtensionInterface;
use Fazland\Notifire\NotifireBuilder;

class ExtendableBuilder extends NotifireBuilder
{
    public function addExtension(ExtensionInterface $extension)
    {
        $extension->register($this);
    }
}