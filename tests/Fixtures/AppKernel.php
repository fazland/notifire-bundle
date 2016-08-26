<?php

namespace Fazland\NotifireBundle\Tests\Fixtures;

use Fazland\NotifireBundle\NotifireBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class AppKernel extends Kernel
{
    /**
     * @var string
     */
    private $configFileName;

    public function __construct($environment, $debug, $configFileName = 'config.xml')
    {
        parent::__construct($environment, $debug);

        $this->configFileName = $configFileName;
    }

    /**
     * {@inheritDoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new SwiftmailerBundle(),
            new NotifireBundle(),
            new TwigBundle(),
            new TestBundle\TestBundle()
        ];
    }

    /**
     * Initializes the service container.
     *
     * The cached version of the service container is used when fresh, otherwise the
     * container is built.
     */
    protected function initializeContainer()
    {
        $class = $this->getContainerClass() . crc32($this->configFileName);

        $cache = new ConfigCache($this->getCacheDir() . '/' . $class . '.php', $this->debug);
        $container = $this->buildContainer();
        $container->compile();
        $this->dumpContainer($cache, $container, $class, $this->getContainerBaseClass());

        require_once $cache->getPath();

        $this->container = new $class();
        $this->container->set('kernel', $this);

        if ($this->container->has('cache_warmer')) {
            $this->container->get('cache_warmer')->warmUp($this->container->getParameter('kernel.cache_dir'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/' . $this->configFileName);
    }
}
