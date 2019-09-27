<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\Builder;

use Fazland\Notifire\NotifireBuilder;
use Fazland\NotifireBundle\Extension\ExtensionInterface;

class ExtendableBuilder extends NotifireBuilder
{
    public function addExtension(ExtensionInterface $extension): void
    {
        $extension->register($this);
    }
}
