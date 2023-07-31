<?php

namespace App\Controller;

use Pimcore\Bundle\AdminBundle\Controller\Admin\LoginController;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\Type\SearchType;
use Pimcore\Model\DataObject\Movie;



class SearchController extends FrontendController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/search", name="search_movie", methods={"GET"})
     */
    public function defaultAction(Request $request, ApplicationLogger $logger): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $moviesListing = new Movie\Listing();
            $logger->info("Search executed");
            return $this->render("search/results.html.twig",["results"=>$moviesListing, "data"=>$data]);
        }


        return $this->render('search/search.html.twig', ["form"=> $form->createView()]);
    }
}
