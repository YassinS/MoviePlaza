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
    name: 'movies:delete:auto',
    description:'DELETES ALL MOVIE DAOS',
    hidden: false
)]
class DeleteAllMovieDaosCommand extends Command{

    public function __construct(private ApplicationLogger $logger, private APIService $APIService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $allMovieDaos = new Movie\Listing();

        foreach ($allMovieDaos as $dao) {
            $output->writeln($dao->getMovieTitle(). " deleted");
            $dao->delete();
        }
        return Command::SUCCESS;

    }
}
