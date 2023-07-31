<?php

namespace App\Controller;

use Pimcore\Bundle\AdminBundle\Controller\Admin\LoginController;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;

class DefaultController extends FrontendController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function defaultAction(Request $request, ApplicationLogger $logger): Response
    {
        $logger->info("Hello World");

        return $this->render('default/default.html.twig');
    }
    public function LogSomeData(ApplicationLogger $logger): void
    {
        $logger->error("HELP");
        for ($i = 0; $i < 10; $i++) {
            $logger->debug("test");
        }
    }


    /**
     * Forwards the request to admin login
     */
    public function loginAction(): Response
    {
        return $this->forward(LoginController::class . '::loginCheckAction');
    }
}
