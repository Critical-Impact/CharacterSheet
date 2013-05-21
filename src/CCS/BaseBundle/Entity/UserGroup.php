<?php

namespace CCS\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserGroup
 */
class UserGroup
{
    /**
     * @var integer
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
