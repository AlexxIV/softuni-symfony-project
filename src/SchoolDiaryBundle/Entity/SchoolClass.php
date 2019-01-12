<?php

namespace SchoolDiaryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SchoolClass
 *
 * @ORM\Table(name="school_class")
 * @ORM\Entity(repositoryClass="SchoolDiaryBundle\Repository\SchoolClassRepository")
 */
class SchoolClass
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int;
     *
     * @ORM\Column(name="class_number_identifier", type="integer", nullable=false)
     *
     * * @Assert\Range(
     *      min = 1,
     *      max = 12,
     *      minMessage = "Please select graden in range [1-12]",
     *      maxMessage = "Please select graden in range [1-12]"
     * )
     */
    private $classNumberIdentifier;

    /**
     * @var string
     *
     * @ORM\Column(name="class_letter_identifier", type="string", nullable=false, length=1)
     */
    private $classLetterIdentifier;

    /**
     *
     * @OneToMany(targetEntity="User", mappedBy="teacherClass")
     */
    private $teacher;

    /**
     * @var ArrayCollection
     *
     * @OneToMany(targetEntity="User", mappedBy="studentClass")
     *
     */
    private $students;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean")
     */
    private $isLocked;

//    /**
//     * @var Schedule
//     *
//     * @OneToOne(targetEntity="Schedule", inversedBy="schoolClass")
//     * @JoinColumn(name="schedule_id", referencedColumnName="id")
//     *
//     */
//    private $schedule;

    public function __construct()
    {
        $this->students = new ArrayCollection();

        $this->teacher = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getClassNumberIdentifier(): int
    {
        return $this->classNumberIdentifier;
    }

    /**
     * @param int $classNumberIdentifier
     */
    public function setClassNumberIdentifier(int $classNumberIdentifier): void
    {
        $this->classNumberIdentifier = $classNumberIdentifier;
    }

    /**
     * @return string
     */
    public function getClassLetterIdentifier(): string
    {
        return $this->classLetterIdentifier;
    }

    /**
     * @param string $classLetterIdentifier
     */
    public function setClassLetterIdentifier(string $classLetterIdentifier): void
    {
        $this->classLetterIdentifier = $classLetterIdentifier;
    }

    /**
     * @return ArrayCollection
     */
    public function getTeacher(): ArrayCollection
    {
        return $this->teacher;
    }

    public function addTeacher(User $teacher)
    {
        $this->teacher[] = $teacher;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getStudents(): ?ArrayCollection
    {
        return $this->students;
    }

    public function addStudent(User $student)
    {
        $this->students[] = $student;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    /**
     * @param bool $isLocked
     */
    public function setIsLocked(bool $isLocked): void
    {
        $this->isLocked = $isLocked;
    }

    public function getGradeForSelect()
    {
        return $this->getClassNumberIdentifier() . $this->getClassLetterIdentifier();
    }



//    /**
//     * @return Schedule
//     */
//    public function getSchedule()
//    {
//        return $this->schedule;
//    }
//
//    /**
//     * @param Schedule $schedule
//     */
//    public function setSchedule(Schedule $schedule): void
//    {
//        $this->schedule = $schedule;
//    }
}

