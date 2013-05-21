<?php

namespace CCS\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @var string
     */
    private $locationType;

    /**
     * @var boolean
     */
    private $uploadToS3;

    /**
     * @var boolean
     */
    private $isUploadedToS3;

    /**
     * @var \DateTime
     */
    private $creationDate;

    /**
     * @var \DateTime
     */
    private $lastModified;

    private $file;

    private $path;

    private $upload_dir;

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }
    public function getAbsoluteUploadedPath()
    {
        return __DIR__ . '/../../../../web/'.$this->getFileLocation();
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        if ($this->upload_dir)
            return $this->upload_dir;
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/general';
    }
    public function setUploadDir($upload_dir)
    {
        $this->upload_dir = $upload_dir;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;


        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }


    public function preUpload()
    {

        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            //$filename = str_replace(' ','_', $this->getFile()->getClientOriginalName());
            $this->path = $filename.'.'.$this->getFile()->guessExtension();
        }
        $this->setFileLocation($this->getWebPath());
        $this->setFileName($this->path);
    }


    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->path);


        // check if we have an old file
//        if (isset($this->temp)) {
//            // delete the old image
//            unlink($this->getUploadRootDir().'/'.$this->temp);
//            // clear the temp image path
//            $this->temp = null;
//        }
        $this->file = null;
    }

    public function removeUpload()
    {;
        if ($file = $this->getAbsoluteUploadedPath()) {
            unlink($file);
        }
    }
    public function removeFile()
    {
        if (file_exists($this->getAbsoluteUploadedPath()))
        {
            unlink($this->getAbsoluteUploadedPath());
        }

    }



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

    /**
     * Set locationType
     *
     * @param string $locationType
     * @return File
     */
    public function setLocationType($locationType)
    {
        $this->locationType = $locationType;

        return $this;
    }

    /**
     * Get locationType
     *
     * @return string
     */
    public function getLocationType()
    {
        return $this->locationType;
    }

    /**
     * Set uploadToS3
     *
     * @param boolean $uploadToS3
     * @return File
     */
    public function setUploadToS3($uploadToS3)
    {
        $this->uploadToS3 = $uploadToS3;

        return $this;
    }

    /**
     * Get uploadToS3
     *
     * @return boolean
     */
    public function getUploadToS3()
    {
        return $this->uploadToS3;
    }

    /**
     * Set isUploadedToS3
     *
     * @param boolean $isUploadedToS3
     * @return File
     */
    public function setIsUploadedToS3($isUploadedToS3)
    {
        $this->isUploadedToS3 = $isUploadedToS3;

        return $this;
    }

    /**
     * Get isUploadedToS3
     *
     * @return boolean
     */
    public function getIsUploadedToS3()
    {
        return $this->isUploadedToS3;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return File
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     * @return File
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;


    /**
     * Set name
     *
     * @param string $name
     * @return File
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
     * @return File
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
     * @var string
     */
    private $fileName;


    /**
     * Set fileName
     *
     * @param string $fileName
     * @return File
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }
}