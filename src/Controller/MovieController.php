<?php

namespace App\Controller;

use Pimcore\Bundle\AdminBundle\Controller\Admin\LoginController;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Symfony\Component\Routing\Annotation\Route;
use \Pimcore\Model\DataObject\Movie;

use App\Form\Type\SearchType;
use Symfony\Component\Form\Form;

class MovieController extends FrontendController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/overview", name="movies_carousel", methods={"GET"})
     */
    public function defaultAction(Request $request, ApplicationLogger $logger): Response
    {
        $form = $this->createForm(SearchType::class);
        if ($form->isSubmitted()) {
            $data = $form->getData();
            $this->forward("App\Controller\SearchController::defaultAction",["query"=>$data]);
        }
        $listingMovies = new Movie\Listing();
        $listingMovies->setOrderKey("popularity");
        $listingMovies->setOrder("desc");
        $listingMovies->setLimit(6);
        $logger->info("Rendering movie Detail page");
        return $this->render('movie/view.html.twig',["movies"=>$listingMovies,"formSearch"=>$form]);
    }

}
