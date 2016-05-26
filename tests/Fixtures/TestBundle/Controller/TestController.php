<?php

namespace Fazland\NotifireBundle\Tests\Fixtures\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class TestController extends Controller
{
    /**
     * @return Response
     */
    public function defaultAction()
    {
        return new Response();
    }
}
