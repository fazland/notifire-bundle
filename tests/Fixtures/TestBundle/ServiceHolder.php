<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\Tests\Fixtures\TestBundle;

class ServiceHolder
{
    private $instance;

    public function __construct($instance)
    {
        $this->instance = $instance;
    }

    /**
     * Gets the held service instance.
     *
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }
}
