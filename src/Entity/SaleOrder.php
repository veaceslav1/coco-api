<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ApiResource(
 *   normalizationContext={"groups"={"saleorder:read"}},
 *   denormalizationContext={"groups"={"saleorder:write"}},
 *   attributes={
 *     "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_CUSTOMER')",
 *     "pagination_items_per_page"=30
 *   },
 *   collectionOperations={
 *     "get"={
 *       "path"="/orders"
 *     },
 *     "post"={"path"="/orders"}
 *   },
 *   itemOperations={
 *     "get"={"path"="/orders/{id}"},
 *     "put"={"path"="/orders/{id}"},
 *     "delete"={
 *       "path"="/orders/{id}",
 *       "security"="is_granted('ROLE_ADMIN')"
 *       }
 *   }
 * )
 *
 * @ORM\Entity(repositoryClass="App\Repository\SaleOrderRepository")
 */
class SaleOrder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"saleorder:read", "saleorder:write"})
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"saleorder:read", "saleorder:write"})
     */
    private $company;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

}
