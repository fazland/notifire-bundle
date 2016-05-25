<?php

namespace Fazland\NotifireBundle\Tests;

use Fazland\NotifireBundle\Tests\Fixtures\AppKernel;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class NotifireBundleTest extends WebTestCase
{
    protected static function createKernel(array $options = array())
    {
        return new AppKernel('test', true);
    }
}
