<?php

namespace App\Entity;

use App\Repository\AcademicHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AcademicHistoryRepository::class)]
class AcademicHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $finalGrade = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null; // e.g., 'Passed', 'Failed', 'In Progress'

    #[ORM\ManyToOne(inversedBy: 'academicHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'academicHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Semester $semester = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFinalGrade(): ?string
    {
        return $this->finalGrade;
    }

    public function setFinalGrade(string $finalGrade): static
    {
        $this->finalGrade = $finalGrade;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getSemester(): ?Semester
    {
        return $this->semester;
    }

    public function setSemester(?Semester $semester): static
    {
        $this->semester = $semester;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }
}
