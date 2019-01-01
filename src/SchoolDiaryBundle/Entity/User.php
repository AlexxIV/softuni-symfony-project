<?php

namespace SchoolDiaryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="SchoolDiaryBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="The Email field cannot be empty")
     * @Assert\Email(message="Please enter a valid email")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     *
     * @Assert\NotBlank(message="The Password cannot be empty")
     *
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255)
     *
     * @Assert\NotBlank(message="The First Name field cannot be empty")
     *
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255)
     *
     * @Assert\NotBlank(message="The Last Name field cannot be empty")
     *
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="personalID", type="string", length=255)
     *
     * @Assert\NotBlank(message="The Personal ID field cannot be empty")
     */
    private $personalID;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     *
     * @ORM\JoinTable(name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *   )
     */
    private $roles;

    /**
     * @var bool
     */
    private $isTeacher;

    /**
     * @var SchoolClass
     *
     * @ORM\OneToOne(targetEntity="SchoolClass", mappedBy="teacher")
     */
    private $teacherClass;

    /**
     * @var SchoolClass
     *
     * @ORM\ManyToOne(targetEntity="SchoolClass", inversedBy="students")
     * @ORM\JoinColumn(name="school_class_id", referencedColumnName="id", nullable=true)
     */
    private $studentClass;

    /**
     * @var string;
     *
     * @ORM\Column(name="grade", type="string", nullable=true)
     *
     * * @Assert\Range(
     *      min = 1,
     *      max = 12,
     *      minMessage = "Please select graden in range [1-12]",
     *      maxMessage = "Please select graden in range [1-12]"
     * )
     */

    private $grade;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="PersonalGrades")
     * @ORM\JoinTable(name="students_grades",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="personal_grade_id", referencedColumnName="id", unique=true)})
     */
    private $personalGrades;

    public function __construct()
    {
        $this->roles = new ArrayCollection();

        $this->personalGrades = new ArrayCollection();
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
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Return the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getPersonalID()
    {
        return $this->personalID;
    }

    /**
     * @param string $personalID
     */
    public function setPersonalID(string $personalID): void
    {
        $this->personalID = $personalID;
    }

    public function getRoles()
    {
        $stringRoles = [];

        foreach ($this->roles as $role) {
            /** @var Role $role */
            $stringRoles[] = $role->getRole();
        }

        return $stringRoles;
    }

    public function getIsTeacher()
    {
        return $this->isTeacher;
    }

    public function setIsTeacher(bool $isTeacher): void
    {
        $this->isTeacher = $isTeacher;
    }

    public function addRole(Role $role)
    {
        $this->roles[] = $role;
        return $this;
    }

    public function isAdmin()
    {
        return in_array("ROLE_ADMIN", $this->getRoles());
    }

    public function isTeacher()
    {
        return in_array("ROLE_TEACHER", $this->getRoles());
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return SchoolClass
     */
    public function getTeacherClass()
    {
        return $this->teacherClass;
    }

    /**
     * @param SchoolClass $teacherClass
     */
    public function setTeacherClass(SchoolClass $teacherClass): void
    {
        $this->teacherClass = $teacherClass;
    }

    /**
     * @return SchoolClass
     */
    public function getStudentClass()
    {
        return $this->studentClass;
    }

    /**
     * @param SchoolClass $studentClass
     */
    public function setStudentClass(SchoolClass $studentClass): void
    {
        $this->studentClass = $studentClass;
    }

    /**
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param string $grade
     */
    public function setGrade(string $grade): void
    {
        $this->grade = $grade;
    }
}

