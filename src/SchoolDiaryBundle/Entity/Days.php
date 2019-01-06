<?php

namespace SchoolDiaryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Days
 *
 * @ORM\Table(name="days")
 * @ORM\Entity(repositoryClass="SchoolDiaryBundle\Repository\DaysRepository")
 */
class Days
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
     * @var Schedule
     *
     * @ORM\ManyToMany(targetEntity="Schedule", mappedBy="days")
     *
     */
    private $schedule;

    /**
     * @var string
     * @ORM\Column(name="day", type="string")
     */
    private $day;

    /**
     * @var string
     * @ORM\Column(name="first", type="string", nullable=true)
     */
    private $first;
    /**
     * @var string
     * @ORM\Column(name="second", type="string", nullable=true)
     */
    private $second;
    /**
     * @var string
     * @ORM\Column(name="third", type="string", nullable=true)
     */
    private $third;
    /**
     * @var string
     * @ORM\Column(name="fourth", type="string", nullable=true)
     */
    private $fourth;
    /**
     * @var string
     * @ORM\Column(name="fifth", type="string", nullable=true)
     */
    private $fifth;
    /**
     * @var string
     * @ORM\Column(name="sixth", type="string", nullable=true)
     */
    private $sixth;
    /**
     * @var string
     * @ORM\Column(name="seventh", type="string", nullable=true)
     */
    private $seventh;

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

    /**
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param string $day
     */
    public function setDay(string $day): void
    {
        $this->day = $day;
    }

    /**
     * @return string
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * @param string $first
     */
    public function setFirst(string $first): void
    {
        $this->first = $first;
    }

    /**
     * @return string
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * @param string $second
     */
    public function setSecond(string $second): void
    {
        $this->second = $second;
    }

    /**
     * @return string
     */
    public function getThird()
    {
        return $this->third;
    }

    /**
     * @param string $third
     */
    public function setThird(string $third): void
    {
        $this->third = $third;
    }

    /**
     * @return string
     */
    public function getFourth()
    {
        return $this->fourth;
    }

    /**
     * @param string $fourth
     */
    public function setFourth(string $fourth): void
    {
        $this->fourth = $fourth;
    }

    /**
     * @return string
     */
    public function getFifth()
    {
        return $this->fifth;
    }

    /**
     * @param string $fifth
     */
    public function setFifth(string $fifth): void
    {
        $this->fifth = $fifth;
    }

    /**
     * @return string
     */
    public function getSixth()
    {
        return $this->sixth;
    }

    /**
     * @param string $sixth
     */
    public function setSixth(string $sixth): void
    {
        $this->sixth = $sixth;
    }

    /**
     * @return string
     */
    public function getSeventh()
    {
        return $this->seventh;
    }

    /**
     * @param string $seventh
     */
    public function setSeventh(string $seventh): void
    {
        $this->seventh = $seventh;
    }


}

