<?php

namespace CCS\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * File
 */
class File
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $fileLocation;


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
     * Set fileLocation
     *
     * @param string $fileLocation
     * @return File
     */
    public function setFileLocation($fileLocation)
    {
        $this->fileLocation = $fileLocation;
    
        return $this;
    }

    /**
     * Get fileLocation
     *
     * @return string 
     */
    public function getFileLocation()
    {
        return $this->fileLocation;
    }
}
