<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $credits = null;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: Section::class)]
    private Collection $sections;

    #[ORM\ManyToMany(targetEntity: AcademicGrade::class, mappedBy: 'subjects')]
    private Collection $academicGrades;

    #[ORM\ManyToMany(targetEntity: Teacher::class, mappedBy: 'subjects')]
    private Collection $teachers;

    #[ORM\ManyToMany(targetEntity: Student::class, mappedBy: 'subjects')]
    private Collection $students;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->academicGrades = new ArrayCollection();
        $this->teachers = new ArrayCollection();
        $this->students = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCredits(): ?int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): static
    {
        $this->credits = $credits;

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
            $section->setSubject($this);
        }

        return $this;
    }

    public function removeSection(Section $section): static
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getSubject() === $this) {
                $section->setSubject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AcademicGrade>
     */
    public function getAcademicGrades(): Collection
    {
        return $this->academicGrades;
    }

    public function addAcademicGrade(AcademicGrade $academicGrade): static
    {
        if (!$this->academicGrades->contains($academicGrade)) {
            $this->academicGrades->add($academicGrade);
            $academicGrade->addSubject($this);
        }

        return $this;
    }

    public function removeAcademicGrade(AcademicGrade $academicGrade): static
    {
        if ($this->academicGrades->removeElement($academicGrade)) {
            $academicGrade->removeSubject($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Teacher>
     */
    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function addTeacher(Teacher $teacher): static
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers->add($teacher);
            $teacher->addSubject($this);
        }

        return $this;
    }

    public function removeTeacher(Teacher $teacher): static
    {
        if ($this->teachers->removeElement($teacher)) {
            $teacher->removeSubject($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->addSubject($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            $student->removeSubject($this);
        }

        return $this;
    }
}
