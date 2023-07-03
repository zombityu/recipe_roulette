<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dayOfCooking = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfCooking(): ?\DateTimeInterface
    {
        return $this->dayOfCooking;
    }

    public function setDayOfCooking(\DateTimeInterface $dayOfCooking): static
    {
        $this->dayOfCooking = $dayOfCooking;

        return $this;
    }
}
