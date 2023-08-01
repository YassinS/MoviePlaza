<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Pimcore\Model\DataObject\Movie;
use Pimcore\Model\DataObject\Data\ExternalImage;

use App\Service\APIService;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'movies:create:auto',
    description:'automatically fetches movies and creates new DAOs',
    hidden: false
)]
class CreateMovieDaosCommand extends Command{

    public function __construct(private ApplicationLogger $logger, private APIService $APIService)
    {
        parent::__construct();
    }

     public function createMovieAction($movie)
    {

        $movieDataObject = new Movie();
        $movieDataObject->setMovieTitle($movie["title"]);
        $movieDataObject->setDescription($movie["overview"]);
        $img = new ExternalImage("https://image.tmdb.org/t/p/original".$movie["backdrop"]);
        $movieDataObject->setBackdrop($img);
        $movieDataObject->setId($movie["id"]);
        $movieDataObject->setParentId(1);
        $movieDataObject->setPopularity($movie["popularity"]);
        $movieTitle = $movie["title"];
        $movieTitle = str_replace(array("/","\""),'',$movieTitle);
        if (Movie::getByPath("/$movieTitle") == null) {
             $movieDataObject->setKey($movieTitle);
        } else{
            $movieDataObject->setKey($movieTitle. "2");
        }

        $movieDataObject->setPublished(true);
        $movieDataObject->save();
        $this->logger->debug("Movie added". " " .$movie["title"]);

    }

    protected function configure(): void{
        $this->addArgument("startpage", InputArgument::REQUIRED, "REQUIRED: Page from which the utility should start fetching");
        $this->addArgument("numofpages", InputArgument::REQUIRED,"REQUIRED: Number of Pages of top-rated movies to fetch and create");
        $this->addArgument('movietitle', InputArgument::OPTIONAL, "OPTIONAL: Movie Name to add Movie manually");

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Adding movies now");
        $numOfPages = (int) $input->getArgument("numofpages");
        $startPage = (int) $input->getArgument("startpage");
        $output->writeln("Number of Pages = $numOfPages");
        for ($i=$startPage; $i <= $startPage+$numOfPages; $i++) {
            $topRatedRes = $this->APIService->getTopRatedMovies($i);
            $topRatedRes = json_decode($topRatedRes);

            foreach ($topRatedRes->results as $movie) {
                $output->writeln($i);
                $resArray = ["id"=>$movie->id,"title"=>$movie->original_title,"backdrop"=>$movie->poster_path,"overview"=>$movie->overview, "popularity"=>$movie->popularity];
                if ($movie->original_language == "en") {
                    $this->createMovieAction($resArray);
                    $output->writeln($movie->original_title);
                }

            }
    }

        return Command::SUCCESS;


}
}
