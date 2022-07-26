<?php

namespace App\Entity;

use App\Repository\DeathNoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DeathNoteRepository::class)]
class DeathNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    public ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['quotes'])]
    public ?string $name = null;

    #[ORM\Column(length: 5)]
    #[Groups(['quotes'])]
    public ?int $bornYear = null;

    #[ORM\Column(type: Types::TEXT)]
    public ?string $cityOfBorn = null;

    #[ORM\Column(length: 5)]
    public ?int $deadYear = null;

    #[ORM\Column(type: Types::TEXT)]
    public ?string $cityOfDead = null;

    #[ORM\Column(length: 5)]
    public ?int $age = null;

    #[ORM\Column(length: 5)]
    public ?int $deadNYearsAgo = null;

    #[ORM\Column(length: 5)]
    public ?int $now = null;
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

    public function getBornYear(): ?int
    {
        return $this->bornYear;
    }

    public function setBornYear(int $bornYear): self
    {
        $this->bornYear = $bornYear;

        return $this;
    }

    public function getCityOfBorn(): ?string
    {
        return $this->cityOfBorn;
    }

    public function setCityOfBorn(string $cityOfBorn): self
    {
        $this->cityOfBorn = $cityOfBorn;

        return $this;
    }

    public function getDeadYear(): ?int
    {
        return $this->deadYear;
    }

    public function setDeadYear(int $deadYear): self
    {
        $this->deadYear = $deadYear;

        return $this;
    }

    public function getCityOfDead(): ?string
    {
        return $this->cityOfDead;
    }

    public function setCityOfDead(string $cityOfDead): self
    {
        $this->cityOfDead = $cityOfDead;

        return $this;
    }

    public function getAge(): ?int
    {
        if ($this->bornYear > $this->deadYear)
            $this->age = 0;
        else
            $this->age = $this->deadYear - $this->bornYear;
        return $this->age;
    }

    public function setAge(int $born_year, int $dead_year): self
    {
        if ($born_year > $dead_year)
            $this->age = 0;
        else
            $this->age = $dead_year - $born_year;

        return $this;
    }
    public function getDeadNYearsAgo(): ?int
    {
        $this->deadNYearsAgo = (int)date('Y') - $this->deadYear;
        return $this->deadNYearsAgo;
    }

    public function getNow(): ?int
    {
        $this->now = date('Y');
        return $this->now;
    }



    public function __construct(string $Name, int $born_year, string $city_of_born, int $dead_year, string $city_of_dead) {
        $this->name         = $Name;
        $this->cityOfBorn = $city_of_born;
        $this->cityOfDead = $city_of_dead;
        $this->bornYear    = $born_year;
        if ($born_year > $dead_year){
            $this->age       = 0;
            $this->deadYear = $born_year;
        }
        else{
            $this->deadYear = $dead_year;
            $this->age       = $dead_year - $born_year;
        }
        $this->now = date('Y');
        $this->deadNYearsAgo = $this->now - $this->deadYear;
    }
}
