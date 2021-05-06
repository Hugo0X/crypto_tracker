<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\ApiTrackerController;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ContractRepository::class)
 */
class Contract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Crypto::class, inversedBy="contracts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $crypto;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="ContractEntity.not_blank")
     * @Assert\Length(
     *      min = 1,
     *      max = 11,
     *      minMessage = "ContractEntity.minLenght",
     *      maxMessage = "ContractEntity.maxLenght",
     *      allowEmptyString = false
     * )
     * @Assert\Positive(
     *      message = "ContractEntity.positive"
     * )
     */
    private $price;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="ContractEntity.not_blank")
     * @Assert\Length(
     *      min = 1,
     *      max = 40,
     *      minMessage = "ContractEntity.minLenght",
     *      maxMessage = "ContractEntity.maxLenght",
     *      allowEmptyString = false
     * )
     * @Assert\Positive(message = "ContractEntity.positive")
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCrypto(): ?crypto
    {
        return $this->crypto;
    }

    public function setCrypto(?crypto $crypto): self
    {
        $this->crypto = $crypto;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    // $this->setUpdatedAt( new \DateImmutable);
}
