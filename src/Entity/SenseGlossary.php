<?php

namespace Maalls\JMDictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(options={"charset"="utf8mb4", "collate"="utf8mb4_bin"})
 * @ORM\Entity(repositoryClass="Maalls\JMDictBundle\Repository\SenseGlossaryRepository")
 */
class SenseGlossary
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    // add your own fields

    /**
     * @ORM\ManyToOne(targetEntity="Sense", inversedBy="senseGlossaries")
     * @ORM\JoinColumn(name="sense_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $sense;

    /**
     * @ORM\Column(type="text")
     */
    private $glossary;


    public function __toString()
    {

        return $this->getGlossary();

    }
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSense()
    {
        return $this->sense;
    }

    /**
     * @param mixed $sense
     *
     * @return self
     */
    public function setSense($sense)
    {
        $this->sense = $sense;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGlossary()
    {
        return $this->glossary;
    }

    /**
     * @param mixed $glossary
     *
     * @return self
     */
    public function setGlossary($glossary)
    {
        $this->glossary = $glossary;

        return $this;
    }
}
