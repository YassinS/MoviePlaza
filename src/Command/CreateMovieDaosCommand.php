<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Pimcore\Model\DataObject\Movie;
use Pimcore\Model\DataObject\Data\ExternalImage;

use App\Service\APIService;

#[AsCommand(
    name: 'movies:create:many',
    description:'automatically fetches movies and creates new DAOs',
    hidden: false
)]
class CreateMovieDaosCommand extends Command{

    private $publicDir;

    public function __construct(private ApplicationLogger $logger, private APIService $APIService, string $publicDir)
    {
        $this->publicDir = $publicDir;
        parent::__construct();
    }

    private function csvToAssoc($csvPath){
        $rows   = array_map('str_getcsv', file($csvPath));
        $header = array_shift($rows);
        $csv    = array();
        foreach($rows as $row) {
            $csv[] = array_combine($header, $row);
        }
        return $csv;
    }

     private function createMovieAction($movie)
    {

        $movieDataObject = new Movie();
        $locations = array();
        foreach ($movie["countries"] as $country) {
            $point = [(float) $country["latitude"], (float) $country["longitude"]];
            $locations[] = $point;
        }
        $allMovieFilmingLocations = new \Pimcore\Model\DataObject\Fieldcollection();
        foreach ($locations as $key => $location) {
            $movieLocationFieldCollection = new \Pimcore\Model\DataObject\Fieldcollection\Data\MovieLocation;
            $movieLocationFieldCollection->setLat($location[0]);
            $movieLocationFieldCollection->setLng($location[1]);
            $allMovieFilmingLocations->add($movieLocationFieldCollection);
        }
        $movieDataObject->setMovieLocation($allMovieFilmingLocations);
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

    private function getGeolocation($countryCode){
        $countriesCSV = "/data/countries.csv";
        $countriesPath = $this->publicDir . $countriesCSV;
        $countriesCSV = $this->csvToAssoc($countriesPath);

        foreach ($countriesCSV as $key => $country) {
            if($countriesCSV[$key]["country"] == $countryCode){
                return ["country"=>$country["name"],"latitude"=>$country["latitude"],"longitude"=>$country["longitude"]];
            }
        }
        return array();


    }

    protected function configure(): void{
        $this->addArgument("startpage", InputArgument::REQUIRED, "REQUIRED: Page from which the utility should start fetching");
        $this->addArgument("numofpages", InputArgument::REQUIRED,"REQUIRED: Number of Pages of top-rated movies to fetch and create");
        $this->addArgument('movieID', InputArgument::OPTIONAL, "OPTIONAL: Movie ID to add Movie manually");

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
                if ($movie->original_language == "en" && !($movie->adult)) {
                    $output->writeln($movie->original_title);
                    $output->writeln($i);
                    $productionCountriesFromApi = json_decode($this->APIService->getMovieDetailsById($movie->id))->production_countries;
                    $productionCountryLatLong = array();
                    foreach ($productionCountriesFromApi as $countryCode) {
                        $movieLocation = $this->getGeolocation($countryCode->iso_3166_1);
                        $productionCountryLatLong[] = $movieLocation;
                    }
                    $resArray = ["id"=>$movie->id,"title"=>$movie->original_title,"backdrop"=>$movie->poster_path,"overview"=>$movie->overview, "popularity"=>$movie->popularity,"countries"=>$productionCountryLatLong];
                    $this->createMovieAction($resArray);

                }

            }
    }

        return Command::SUCCESS;


}
}
