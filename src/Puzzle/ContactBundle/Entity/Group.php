<?php

namespace Puzzle\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="contact_group")
 * @ORM\Entity(repositoryClass="Puzzle\ContactBundle\Repository\GroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Group
{
	/**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Puzzle\UserBundle\Service\KeygenManager")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var string
     *
     * @ORM\Column(name="contacts", type="simple_array", nullable=true)
     */
    private $contacts;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;
    
    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="parent", cascade={"remove"})
     */
    private $childs;
    
    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Group
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set contacts
     *
     * @param array $contacts
     *
     * @return Group
     */
    public function setContacts($contacts)
    {
    	$this->contacts = $contacts;
    	
    	return $this;
    }
    
    
    /**
     * Add contact
     *
     * @param string $contact
     */
    public function addContact($contact){
    	
    	$this->contacts[] = $contact;
    	$this->contacts = array_unique($this->contacts);
    	
    	return $this;
    }
    
    /**
     * Remove contact
     *
     * @param string $contact
     */
    public function removeContact($contact){
    	
    	$this->contacts = array_diff($this->contacts, [$contact]);
    	
    	return $this;
    }
    
    /**
     * Get contacts
     *
     * @return array
     */
    public function getContacts()
    {
    	return $this->contacts;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
    	$this->createdAt = new \DateTime();
    
    	return $this;
    }
    
    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
    	return $this->createdAt;
    }
    
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
        
        return $this;
    }
    
    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->childs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add child
     *
     * @param \Puzzle\ContactBundle\Entity\Group $child
     *
     * @return Group
     */
    public function addChild(\Puzzle\ContactBundle\Entity\Group $child)
    {
        $this->childs[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \Puzzle\ContactBundle\Entity\Group $child
     */
    public function removeChild(\Puzzle\ContactBundle\Entity\Group $child)
    {
        $this->childs->removeElement($child);
    }

    /**
     * Get childs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Set parent
     *
     * @param \Puzzle\ContactBundle\Entity\Group $parent
     *
     * @return Group
     */
    public function setParent(\Puzzle\ContactBundle\Entity\Group $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Puzzle\ContactBundle\Entity\Group
     */
    public function getParent()
    {
        return $this->parent;
    }
}
