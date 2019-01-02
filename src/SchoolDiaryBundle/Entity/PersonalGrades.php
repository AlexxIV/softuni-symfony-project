<?php

namespace SchoolDiaryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PersonalGrades
 *
 * @ORM\Table(name="personal_grades")
 * @ORM\Entity(repositoryClass="SchoolDiaryBundle\Repository\PersonalGradesRepository")
 */
class PersonalGrades
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
     *
     * @ORM\Column(name="grade_name", type="string")
     */
    private $gradeName;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer")
     * * @Assert\Range(
     *      min = 1,
     *      max = 6,
     *      minMessage = "Please select mark in range [1-6]",
     *      maxMessage = "Please select mark in range [1-6]"
     * )
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", nullable=true)
     */
    private $notes;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="personalGrades")
     */
    private $students;

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
    public function getGradeName()
    {
        return $this->gradeName;
    }

    /**
     * @param string $gradeName
     */
    public function setGradeName(string $gradeName): void
    {
        $this->gradeName = $gradeName;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue(int $value): void
    {
        $this->value = $value;
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


}

