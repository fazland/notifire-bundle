<?php

namespace Fazland\NotifireBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NotifireBundle extends Bundle
{
    public function boot()
    {
        $this->container
            ->get('fazland.notifire.builder')
            ->initialize();
    }
}
