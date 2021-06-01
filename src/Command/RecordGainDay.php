<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Controller\ApiTrackerController;
use App\Repository\HistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\History;


class RecordGainDay extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:record-gain';

    private $entityManager;

    public function __construct(EntityManagerInterface $em, HistoryRepository $historyRepository, ApiTrackerController $apiTracker)
    {
        $this->em = $em;
        $this->historyRepository = $historyRepository;
        $this->apiTracker = $apiTracker;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Record the benefit')
            ->setHelp('This command allows you to save the total benefit when you execute it')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // $query = $this->em->createQuery(
            //     'SELECT con.id, cry.name, con.price, con.quantity
            //     FROM App\Entity\Contract con
            //     INNER JOIN  App\Entity\Crypto cry'
            // );
            // $contract = $query->getResult();
            // dd($contract);

            $query = $this->em->createQuery(
                'SELECT cry.name, SUM(con.price) price, SUM(con.quantity) quantity 
                FROM App\Entity\Contract con
                INNER JOIN  App\Entity\Crypto cry
                WHERE con.crypto = cry.id
                GROUP BY con.crypto '
            );
            $sumCrypto = $query->getResult();
            
            $benefit = 0;
            foreach ($sumCrypto as $aSumCrypto) {
                $benefit += $this->apiTracker->getCurrency($aSumCrypto['name']) * $aSumCrypto['quantity'] - $aSumCrypto['price'];
            }

            $history = new History;
            $this->em->persist($history->setBenefit(intval($benefit)));
            $this->em->flush();

            return Command::SUCCESS;
        }
        catch (Exception $e) {
            return Command::FAILURE;
        }
        
    }
}