<?php

namespace SchoolDiaryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="SchoolDiaryBundle\Repository\ScheduleRepository")
 */
class Schedule
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
     * @var SchoolClass
     *
     * @OneToOne(targetEntity="SchoolClass", mappedBy="schedule")
     */
    private $schoolClass;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Days", mappedBy="schedule")
     */
    private $days;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_modified", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $lastModified;

//    /**
//     * @var ArrayCollection
//     *
//     * @ORM\ManyToMany(targetEntity="PersonalGrades")
//     * @ORM\JoinTable(name="students_grades",
//     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
//     *     inverseJoinColumns={@ORM\JoinColumn(name="personal_grade_id", referencedColumnName="id", unique=true)})
//     */
//    private $personalGrades;

    public function __construct()
    {
        $this->days = new ArrayCollection();

        $this->setLastModified(new \DateTime());
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
     * @return SchoolClass
     */
    public function getSchoolClass()
    {
        return $this->schoolClass;
    }

    /**
     * @param SchoolClass $schoolClass
     */
    public function setSchoolClass(SchoolClass $schoolClass): void
    {
        $this->schoolClass = $schoolClass;
    }

    /**
     * @return ArrayCollection
     */
    public function getDays()
    {
        return $this->days;
    }

    public function addDay(Days $day)
    {
        $this->days[] = $day;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastModified(): \DateTime
    {
        return $this->lastModified;
    }

    /**
     * @param \DateTime $lastModified
     */
    public function setLastModified(\DateTime $lastModified): void
    {
        $this->lastModified = $lastModified;
    }


}

