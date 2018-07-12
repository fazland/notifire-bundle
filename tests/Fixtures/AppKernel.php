<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\Tests\Fixtures;

use Fazland\NotifireBundle\NotifireBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
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

    public function getCacheDir()
    {
        return __DIR__.'/cache';
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new SwiftmailerBundle(),
            new NotifireBundle(),
            new TwigBundle(),
            new TestBundle\TestBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/'.$this->configFileName);
    }
}
