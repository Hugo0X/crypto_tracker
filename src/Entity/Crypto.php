<?php

namespace App\Entity;

use App\Repository\CryptoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Controller\ApiTrackerController;


/**
 * @ORM\Entity(repositoryClass=CryptoRepository::class)
 * @UniqueEntity(fields={"name"}, message="Cette crypto monnaie existe déjà")
 */
class Crypto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="ContractEntity.not_blank")
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "ContractEntity.minLenght",
     *      maxMessage = "ContractEntity.maxLenght",
     *      allowEmptyString = false
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=8, unique=true)
     */
    private $acronym;

    /**
     * @ORM\OneToMany(targetEntity=Contract::class, mappedBy="crypto", orphanRemoval=true)
     */
    private $contracts;

    public function __construct()
    {
        $this->contracts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAcronym(): ?string
    {
        return $this->acronym;
    }

    public function setAcronym(string $acronym): self
    {
        $this->acronym = $acronym;

        return $this;
    }

    public function getCryptoImageUrl() : ?string
    {
        $apiTracker = new ApiTrackerController;

        return( $apiTracker->getImageUrl($this->getName()));
    }

    public function getCurrentPrice() : ?float
    {
        $apiTracker = new ApiTrackerController;

        return( $apiTracker->getCurrency($this->getName()));
    }
}
