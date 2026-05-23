<?php

namespace App\Entity;

use App\Repository\RegistrationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
class Registration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(length: 255)]
    private ?string $mealPreference = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dietaryNotes = null;

    #[ORM\Column(length: 255)]
    private ?string $ticketNumber = null;

    #[ORM\Column]
    private ?\DateTime $registeredAt = null;

    #[ORM\ManyToOne]
    private ?Summit $summit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;
        return $this;
    }

    public function getMealPreference(): ?string
    {
        return $this->mealPreference;
    }

    public function setMealPreference(string $mealPreference): static
    {
        $this->mealPreference = $mealPreference;
        return $this;
    }

    public function getDietaryNotes(): ?string
    {
        return $this->dietaryNotes;
    }

    public function setDietaryNotes(?string $dietaryNotes): static
    {
        $this->dietaryNotes = $dietaryNotes;
        return $this;
    }

    public function getTicketNumber(): ?string
    {
        return $this->ticketNumber;
    }

    public function setTicketNumber(string $ticketNumber): static
    {
        $this->ticketNumber = $ticketNumber;
        return $this;
    }

    public function getRegisteredAt(): ?\DateTime
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(\DateTime $registeredAt): static
    {
        $this->registeredAt = $registeredAt;
        return $this;
    }

    public function getSummit(): ?Summit
    {
        return $this->summit;
    }

    public function setSummit(?Summit $summit): static
    {
        $this->summit = $summit;
        return $this;
    }
}