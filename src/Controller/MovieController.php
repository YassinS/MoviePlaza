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
use App\Service\APIService;
use PharIo\Manifest\ApplicationName;
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

    /**
     * @param Request $request
     * @return Response
     * @Route("/movie/{movieId}", name="movie_detail", methods={"GET"})
     */
    public function detailAction(APIService $APIService, ApplicationLogger $logger, $movieId) : Response {
        $movieObject = Movie::getById($movieId);
        if (!($movieObject)) {
            throw $this->createNotFoundException("Movie doesn't live on our servers yet :(");
        }

        $detailsForMovie = $APIService->getMovieDetailsById($movieId);
        $detailsForMovie = json_decode($detailsForMovie);
        return $this->render("movie/detail.html.twig",["movie"=>$detailsForMovie,"movieLocations"=>$movieObject->getMovieLocation() ]);

    }

}
