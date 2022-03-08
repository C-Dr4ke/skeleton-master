<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass:ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private $id;

    #[ORM\Column(type:"string", length:255)]
    #[Assert\NotBlank(message:"Veuillez saisir ce champs")]
    private $title;

    #[ORM\Column(type:"string", length:255)]
    #[Assert\NotBlank(message:"Veuillez saisir ce champs")]
    private $description;

    #[ORM\Column(type:"string", length:255)]
    #[Assert\NotBlank(message:"Veuillez saisir ce champs")]
    private $protein;

    #[ORM\Column(type:"float")]
    #[Assert\NotBlank(message:"Veuillez saisir ce champs")]
    private $price;

    #[ORM\Column(type:"string", length:255)]
    #[Assert\NotBlank(message:"Veuillez saisir ce champs")]
    private $picture;

    #[ORM\ManyToOne(targetEntity:SubCategory::class, inversedBy:"products")]
    private $subCategory;

    public $editPicture;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getProtein(): ?string
    {
        return $this->protein;
    }

    public function setProtein(string $protein): self
    {
        $this->protein = $protein;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getSubCategory(): ?SubCategory
    {
        return $this->subCategory;
    }

    public function setSubCategory(?SubCategory $subCategory): self
    {
        $this->subCategory = $subCategory;

        return $this;
    }

}
