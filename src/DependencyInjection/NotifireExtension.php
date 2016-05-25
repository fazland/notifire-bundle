<?php

namespace Fazland\NotifireBundle\DependencyInjection;

use Fazland\Notifire\EventSubscriber\Email\SwiftMailerHandler;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\Sms;
use Fazland\Notifire\NotifireBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class NotifireExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        /** @var NotifireBuilder $notifireBuilder */
        $notifireBuilder = $container->get('fazland.notifire.builder')->create();

        $dispatcher = $container->get('event_dispatcher');

        $notifireBuilder
            ->setDispatcher($dispatcher)
        ;

        if (class_exists('Swift_mailer')) {
            $transport = \Swift_SmtpTransport::newInstance('localhost', 25);
            $mailer = \Swift_Mailer::newInstance($transport);
            $dispatcher->addSubscriber(new SwiftMailerHandler($mailer));
            $notifireBuilder->addNotification('email', Email::class);
        }
    }
}
