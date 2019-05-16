<?php

namespace Puzzle\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\AdminBundle\Traits\DatetimeTrait;

/**
 * Contact
 *
 * @ORM\Table(name="contact_contact")
 * @ORM\Entity(repositoryClass="Puzzle\ContactBundle\Repository\ContactRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Contact
{
    use DatetimeTrait;
    
    /**
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Puzzle\UserBundle\Service\KeygenManager") 
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;
    
    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     */
    private $picture;
    
    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=255, nullable=true)
     */
    private $company;
    
    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255, nullable=true)
     */
    private $position;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    public function getId() :? string {
        return $this->id;
    }

    public function setEmail($email) : self {
        $this->email = $email;
        return $this;
    }

    public function getEmail() :? string {
        return $this->email;
    }
    
    public function setPhone($phone) : self {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone() :? string {
        return $this->phone;
    }

    public function setLocation($location) : self {
        $this->location = $location;
        return $this;
    }

    public function getLocation() :? string {
        return $this->location;
    }

    public function setCompany($company) : self {
        $this->company = $company;
        return $this;
    }

    public function getCompany() :? string {
        return $this->company;
    }

    public function setPosition($position) : self {
        $this->position = $position;
        return $this;
    }

    public function getPosition() :? int {
        return $this->position;
    }

    public function setFirstName($firstName) : string {
        $this->firstName = $firstName;
        return $this;
    }
    
    public function getFirstName() :? string {
        return $this->firstName;
    }

    public function setLastName($lastName) : self {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLastName() :? string {
        return $this->lastName;
    }

    public function setPicture($picture) :? string {
        $this->picture = $picture;
        return $this;
    }

    public function getPicture() :? string {
        return $this->picture;
    }
    
    public function getFullName() :? string {
        return trim($this->firstName. ' '. $this->lastName);
    }
}
