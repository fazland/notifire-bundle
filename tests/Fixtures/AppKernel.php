<?php

namespace Fazland\NotifireBundle\Tests\Fixtures;

use Fazland\NotifireBundle\NotifireBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class AppKernel extends Kernel
{
    /**
     * {@inheritDoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new SwiftmailerBundle(),
            new NotifireBundle(),
            new TestBundle\TestBundle()
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.xml');
    }
}
