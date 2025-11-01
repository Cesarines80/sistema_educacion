<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\OneToOne(inversedBy: 'student', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AcademicGrade $academicGrade = null;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Grade::class)]
    private Collection $grades;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: AcademicHistory::class)]
    private Collection $academicHistories;

    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'students')]
    private Collection $subjects;

    public function __construct()
    {
        $this->grades = new ArrayCollection();
        $this->academicHistories = new ArrayCollection();
        $this->subjects = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAcademicGrade(): ?AcademicGrade
    {
        return $this->academicGrade;
    }

    public function setAcademicGrade(?AcademicGrade $academicGrade): static
    {
        $this->academicGrade = $academicGrade;

        return $this;
    }

    /**
     * @return Collection<int, Grade>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grade $grade): static
    {
        if (!$this->grades->contains($grade)) {
            $this->grades->add($grade);
            $grade->setStudent($this);
        }

        return $this;
    }

    public function removeGrade(Grade $grade): static
    {
        if ($this->grades->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getStudent() === $this) {
                $grade->setStudent(null);
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
            $academicHistory->setStudent($this);
        }

        return $this;
    }

    public function removeAcademicHistory(AcademicHistory $academicHistory): static
    {
        if ($this->academicHistories->removeElement($academicHistory)) {
            // set the owning side to null (unless already changed)
            if ($academicHistory->getStudent() === $this) {
                $academicHistory->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        $this->subjects->removeElement($subject);

        return $this;
    }
}
