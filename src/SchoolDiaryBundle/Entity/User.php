<?php

namespace SchoolDiaryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="SchoolDiaryBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
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
     * @ORM\JoinTable(name="users_roles")
     */
    private $roles;

    /**
     * @OneToOne(targetEntity="SchoolClass", mappedBy="teacher")
     */
    private $teacherClass;

    /**
     * @var SchoolClass
     *
     * @ORM\ManyToOne(targetEntity="SchoolClass", inversedBy="students")
     * @ORM\JoinColumn(name="student_class", referencedColumnName="id")
     */
    private $studentClass;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="PersonalGrades", inversedBy="student")
     * @ORM\JoinTable(name="students_grades",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="personal_grade_id", referencedColumnName="id", unique=true)})
     */
    private $personalGrades;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Absences", inversedBy="student")
     * @ORM\JoinTable(name="students_absences",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="absence_id", referencedColumnName="id", unique=true)})
     */
    private $absences;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=false)
     */
    private $image;

    /**
     * @var bool
     *
     * @ORM\Column(name="confirmed", type="boolean")
     */
    private $confirmed;


    public function __construct()
    {
        $this->roles = new ArrayCollection();

        $this->personalGrades = new ArrayCollection();

        $this->absences = new ArrayCollection();
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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * Return the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
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

    public function addRole(Role $role)
    {
        $this->roles[] = $role;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTeacherClass()
    {
        return $this->teacherClass;
    }

    /**
     * @param mixed $teacherClass
     */
    public function setTeacherClass($teacherClass): void
    {
        $this->teacherClass = $teacherClass;
    }



    /**
     * @return SchoolClass
     */
    public function getStudentClass(): ?SchoolClass
    {
        return $this->studentClass;
    }

    public function setStudentClass($studentClass): void
    {
        $this->studentClass = $studentClass;
    }

    public function getPersonalGrades()
    {
        return $this->personalGrades;
    }

    public function addPersonalGrade(PersonalGrades $grade) {
        $this->personalGrades[] = $grade;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAbsences()
    {
        return $this->absences;
    }

    public function addAbsence(Absences $absence) {
        $this->absences[] = $absence;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed(bool $confirmed): void
    {
        $this->confirmed = $confirmed;
    }


    public function isAdmin(): bool
    {
        return \in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    public function isTeacher(): bool
    {
        return \in_array('ROLE_TEACHER', $this->getRoles(), true);
    }

    public function isStudent(): bool
    {
        return \in_array('ROLE_USER', $this->getRoles(), true);
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, array('allowed_classes' => false));
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
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        return null;
    }

    public function serializer() {
        $encoder = new JsonEncoder();

        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array(
            'admin', 'password', 'username',
            'salt', 'roles', 'teacher', 'teacherClass'));

        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $serializer = new Serializer(array($normalizer), array($encoder));
        return $serializer->serialize($this, 'json');
    }
}

