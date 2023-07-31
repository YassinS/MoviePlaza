<?php

namespace App\Controller;

use Pimcore\Bundle\AdminBundle\Controller\Admin\LoginController;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Symfony\Component\Routing\Annotation\Route;
use \Pimcore\Model\DataObject\Movie;


class MovieController extends FrontendController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/overview", name="movies_carousel", methods={"GET"})
     */
    public function defaultAction(Request $request, ApplicationLogger $logger): Response
    {
        $listingMovies = new Movie\Listing();
        $listingMovies->setOrderKey("popularity");
        $listingMovies->setOrder("desc");
        $listingMovies->setLimit(6);
        $logger->info("Rendering movie Detail page");

        return $this->render('movie/view.html.twig',["movies"=>$listingMovies]);
    }


    /**
     * Forwards the request to admin login
     */
    public function loginAction(): Response
    {
        return $this->forward(LoginController::class . '::loginCheckAction');
    }
}
