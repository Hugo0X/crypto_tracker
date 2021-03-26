<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\ApiTrackerController;


/**
 * @ORM\Entity(repositoryClass=HistoryRepository::class)
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $benefit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBenefit(): ?int
    {
        return $this->benefit;
    }

    public function setBenefit(int $benefit): self
    {
        $this->benefit = $benefit;

        return $this;
    }

    public function getAllBenefit(EntityManagerInterface $em) : ?int
    {
        $query = $em->createQuery(
            'SELECT cry.name, SUM(con.price) price, SUM(con.quantity) quantity, 
            FROM App\Entity\Contract con
            INNER JOIN  App\Entity\Crypto cry
            GROUP BY con.crypto '
        );
        $sumCrypto = $query->getResult();

        $apiTracker = new ApiTrackerController;

        foreach ($sumCrypto as $aSumCrypto)
        {
            $benefit += $apiTracker->getCurrency($aSumCrypto['name']) * $aSumCrypto['quantity'] - $aSumCrypto['price'];
        }

        return $this->setBenefit($benefit);
    }
}
