<?php

namespace App\Entity;

use App\Repository\SemesterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SemesterRepository::class)]
class Semester
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\OneToMany(mappedBy: 'semester', targetEntity: Section::class)]
    private Collection $sections;

    #[ORM\OneToMany(mappedBy: 'semester', targetEntity: AcademicHistory::class)]
    private Collection $academicHistories;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->academicHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): static
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->setSemester($this);
        }

        return $this;
    }

    public function removeSection(Section $section): static
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getSemester() === $this) {
                $section->setSemester(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AcademicHistory>
     */
    public function getAcademicHistories(): Collection
    {
        return $this->academicHistories;
    }

    public function addAcademicHistory(AcademicHistory $academicHistory): static
    {
        if (!$this->academicHistories->contains($academicHistory)) {
            $this->academicHistories->add($academicHistory);
            $academicHistory->setSemester($this);
        }

        return $this;
    }

    public function removeAcademicHistory(AcademicHistory $academicHistory): static
    {
        if ($this->academicHistories->removeElement($academicHistory)) {
            // set the owning side to null (unless already changed)
            if ($academicHistory->getSemester() === $this) {
                $academicHistory->setSemester(null);
            }
        }

        return $this;
    }
}
