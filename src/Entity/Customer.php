<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @UniqueEntity(
 *     fields={"supplier", "name"},
 *     errorPath="name",
 *     message="Un client existe déjà à ce nom."
 * )
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_customers"})
     * @SWG\Property(description="The unique identifier of the customer.")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom du client doit être renseigné")
     * @Groups({"get_customers"})
     * @SWG\Property(type="string", 
     *      maxLength=255, 
     *      description="The name of the customer. Must be unique by supplier."
     * )
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Supplier::class, inversedBy="customers")
     * @ORM\JoinColumn(nullable=false)
     * @SWG\Schema(ref=@Model(type=Supplier::class))
     */
    private $supplier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }
}
