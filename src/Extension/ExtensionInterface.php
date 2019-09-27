<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\Extension;

use Fazland\Notifire\NotifireBuilder;

/**
 * Represents a Notifire extension
 * Can be used to register notifications into the Notifire instance.
 *
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
interface ExtensionInterface
{
    /**
     * Register notifications into the {@see NotifireBuilder}.
     *
     * @param NotifireBuilder $builder
     */
    public function register(NotifireBuilder $builder): void;
}
