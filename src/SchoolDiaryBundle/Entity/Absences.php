<?php

namespace SchoolDiaryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Absences
 *
 * @ORM\Table(name="absences")
 * @ORM\Entity(repositoryClass="SchoolDiaryBundle\Repository\AbsencesRepository")
 */
class Absences
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="excused", type="boolean", options={"default": "0"})
     */
    private $excused;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", nullable=true)
     */
    private $notes;

    /**
     * @var string
     *
     * @ORM\Column(name="course", type="string")
     */
    private $course;

    /**
     * @var User
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="absences")
     */
    private $student;

    public function __construct()
    {
        $this->setDate(new \DateTime());
        $this->setExcused(false);
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Absences
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set excused
     *
     * @param boolean $excused
     *
     * @return Absences
     */
    public function setExcused($excused)
    {
        $this->excused = $excused;

        return $this;
    }

    /**
     * Get excused
     *
     * @return bool
     */
    public function getExcused()
    {
        return $this->excused;
    }

    /**
     * @return string
     */
    public function getCourse(): string
    {
        return $this->course;
    }

    /**
     * @param string $course
     */
    public function setCourse(string $course): void
    {
        $this->course = $course;
    }



    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes(string $notes): void
    {
        $this->notes = $notes;
    }


    /**
     * @return User
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * @param User $student
     */
    public function setStudent(User $student): void
    {
        $this->student = $student;
    }


}

