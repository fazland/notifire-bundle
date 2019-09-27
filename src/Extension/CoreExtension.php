<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\Extension;

use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\Sms;
use Fazland\Notifire\NotifireBuilder;

/**
 * Holds the core extensions to be registered.
 */
class CoreExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(NotifireBuilder $builder): void
    {
        $builder
            ->addNotification('email', $this->getEmailClass())
            ->addNotification('sms', $this->getSmsClass())
        ;
    }

    protected function getEmailClass(): string
    {
        return Email::class;
    }

    protected function getSmsClass(): string
    {
        return Sms::class;
    }
}
