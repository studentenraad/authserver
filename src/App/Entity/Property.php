<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Property
 *
 * @ORM\Table(name="properties")
 * @ORM\Entity()
 */
class Property
{    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=25, unique=true)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="user_editable", type="boolean")
     */
    private $userEditable;
    
    /**
     * @var boolean 
     * 
     * @ORM\Column(name="required", type="boolean")
     */
    private $required;
        
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
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get userEditable
     *
     * @return boolean 
     */
    public function isUserEditable()
    {
        return $this->userEditable;
    }

    /**
     * Get required
     *
     * @return boolean 
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return UserProperty
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set userEditable
     *
     * @param boolean $userEditable
     * @return UserProperty
     */
    public function setUserEditable($userEditable)
    {
        $this->userEditable = $userEditable;

        return $this;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return UserProperty
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }
}
