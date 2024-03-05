<?php

namespace App\Command;

use App\Entity\Game;
use App\Entity\GameTmp;
use App\Service\Scrapy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'scrapy',
    description: 'Add a short description for your command',
)]
class ScrapyInitCommand extends Command
{
    private $scrapyInit;
    private $em;

    public function __construct(Scrapy $scrapy,EntityManagerInterface $em)
    {
        parent::__construct();
        $this->scrapy=$scrapy;
        $this->em=$em;

    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('nbPages', null, InputOption::VALUE_REQUIRED, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if($input->getArgument('arg')==='init'){
           
            $this->scrapy->getListGames($input->getOption('nbPages'));

            $io->success($this->scrapy->getFlag().' jeuxTmp ont été importés.');

        }
        if($input->getArgument('arg')==='do'){

            $arrayGameTmp=$this->em->getRepository(GameTmp::class)->findAll();

            $bar = new ProgressBar($output,count($arrayGameTmp));

        foreach($arrayGameTmp as $gameTmp){

                $game=$this->scrapy->crawlerDetail($gameTmp);
                $bar->advance();

        }
        }


        $io->success('Now you can unleash the powa of Scrapy the migthy one !!!!');

        return Command::SUCCESS;
    }
}
