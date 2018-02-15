<?php

namespace Maalls\JMDictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(options={"charset"="utf8mb4", "collate"="utf8mb4_bin"})
 * @ORM\Entity(repositoryClass="Maalls\JMDictBundle\Repository\SenseSourceRepository")
 */
class SenseSource
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    // add your own fields

    /**
     * @ORM\ManyToOne(targetEntity="Sense")
     * @ORM\JoinColumn(name="sense_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $sense;

    /**
     * @ORM\Column(type="text")
     */
    private $source;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $language;


    public function setLanguage($language)
    {

        $this->language = $language;

    }

    public function getLanguage()
    {

        return $this->language;

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
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     *
     * @return self
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }
}
