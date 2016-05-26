<?php

namespace Fazland\NotifierBundle\Extension;

use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\Sms;
use Fazland\Notifire\NotifireBuilder;

/**
 * Holds the core extensions to be registered
 */
class CoreExtension implements ExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function register(NotifireBuilder $builder)
    {
        $builder
            ->addNotification('email', $this->getEmailClass())
            ->addNotification('sms', $this->getSmsClass())
            ;
    }

    protected function getEmailClass()
    {
        return Email::class;
    }

    protected function getSmsClass()
    {
        return Sms::class;
    }
}
