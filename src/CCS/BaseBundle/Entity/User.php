<?php

namespace CCS\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
/**
 * User
 */
class User extends BaseUser
{
    /**
     * @var integer
     */
    protected $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
	
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }	
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $files;


    /**
     * Add files
     *
     * @param \CCS\BaseBundle\Entity\File $files
     * @return User
     */
    public function addFile(\CCS\BaseBundle\Entity\File $files)
    {
        $this->files[] = $files;
    
        return $this;
    }

    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * Remove files
     *
     * @param \CCS\BaseBundle\Entity\File $files
     */
    public function removeFile(\CCS\BaseBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }
}