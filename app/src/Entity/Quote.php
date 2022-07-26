<?php

namespace App\Entity;

use App\Repository\QuoteRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: QuoteRepository::class)]
class Quote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    public ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['quotes'])]
    public ?string $quote = null;

    #[ORM\Column(length: 25)]
    public ?string $historian = null;

    #[Groups(['quotes'])]
    #[ORM\Column(length: 5)]
    public ?string $year = null;

    #[Groups(['quotes'])]
    #[ORM\Column(type: Types::TEXT)]
    public ?string $address = null;

    #[ORM\ManyToOne(inversedBy: 'quotes', cascade:['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['quotes'])]
    public ?Author $author = null;



    public function __construct($quote, $historian, $year, $address) {
        $this->quote = $quote;
        $this->historian = $historian;
        $this->year = $year;
        $this->address = $address;
        $author = new Author();
        $author->setName($historian);
        $author->addQuote($this);




    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuote(): ?string
    {
        return $this->quote;
    }

    public function setQuote(string $quote): self
    {
        $this->quote = $quote;

        return $this;
    }

    public function getHistorian(): ?string
    {
        return $this->historian;
    }

    public function setHistorian(string $historian): self
    {
        $this->historian = $historian;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }


    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

   
}
