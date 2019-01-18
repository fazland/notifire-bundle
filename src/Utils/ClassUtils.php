<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\Utils;

class ClassUtils
{
    /**
     * Whether the specified class exists or not.
     *
     * @param string $className
     *
     * @return bool
     */
    public function exists(string $className): bool
    {
        return \class_exists($className);
    }
}
