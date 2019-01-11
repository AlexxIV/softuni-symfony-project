<?php

namespace SchoolDiaryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;

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
     * @var string
     * @ORM\Column(name="name", type="string", unique=true)
     */
    private $name;

    /**
     * @var User
     *
     * @OneToOne(targetEntity="User", mappedBy="teacherClass")
     * @JoinColumn(name="teacher", referencedColumnName="id")
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
     * @var Schedule
     *
     * @OneToOne(targetEntity="Schedule", inversedBy="schoolClass")
     * @JoinColumn(name="schedule_id", referencedColumnName="id")
     *
     */
    private $schedule;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return User
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * @param User $teacher
     */
    public function setTeacher(User $teacher): void
    {
        $this->teacher = $teacher;
    }

    /**
     * @return ArrayCollection
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * @param ArrayCollection $students
     */
    public function setStudents(ArrayCollection $students): void
    {
        $this->students = $students;
    }

    /**
     * @return Schedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * @param Schedule $schedule
     */
    public function setSchedule(Schedule $schedule): void
    {
        $this->schedule = $schedule;
    }
}

