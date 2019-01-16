<?php

namespace SchoolDiaryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DayRecord
 *
 * @ORM\Table(name="day_record")
 * @ORM\Entity(repositoryClass="SchoolDiaryBundle\Repository\DayRecordRepository")
 */
class DayRecord
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
     * @var int
     *
     * @ORM\Column(name="identifier", type="integer")
     */
    private $identifier;

    /**
     * @var string
     * @ORM\Column(name="value", type="string")
     */
    private $value;

    /**
     * @var Days
     * @ORM\ManyToOne(targetEntity="Days", inversedBy="records")
     * @ORM\JoinColumn(name="day_id", referencedColumnName="id")
     */
    private $day;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->identifier;
    }

    /**
     * @param int $identifier
     */
    public function setIdentifier(int $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return Days
     */
    public function getDay(): Days
    {
        return $this->day;
    }

    /**
     * @param Days $day
     */
    public function setDay(Days $day): void
    {
        $this->day = $day;
    }
}
