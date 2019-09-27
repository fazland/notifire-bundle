<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\Tests\Fixtures\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class TestController extends AbstractController
{
    /**
     * @return Response
     */
    public function defaultAction(): Response
    {
        return new Response();
    }
}
