<?php

namespace Maalls\JMDictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(options={"charset"="utf8mb4", "collate"="utf8mb4_bin"})
 * @ORM\Entity(repositoryClass="Maalls\JMDictBundle\Repository\SenseRepository")
 */
class Sense
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    // add your own fields

    /**
     * @ORM\Column(type="text")
     */
    private $pos = '';

    /**
     * @ORM\Column(type="text")
     */
    private $field = '';

    /**
     * @ORM\Column(type="text")
     */
    private $misc = '';

    /**
     * @ORM\Column(type="text")
     */
    private $dial = '';

    /**
     * @ORM\Column(type="text")
     */
    private $info = '';

    /**
     * @ORM\ManyToOne(targetEntity="Base")
     * @ORM\JoinColumn(name="base_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $base;

    /**
     * @ORM\OneToMany(targetEntity="SenseWord", mappedBy="sense", cascade={"persist"})
     */
    private $senseWords;

    /**
     * @ORM\OneToMany(targetEntity="SenseGlossary", mappedBy="sense", cascade={"persist"})
     */
    private $senseGlossaries;


    public function __construct()
    {

        $this->senseGlossaries = new \Doctrine\Common\Collections\ArrayCollection();

    }

    public function getSenseGlossaries()
    {

        return $this->senseGlossaries;

    }

    public function getSenseWords()
    {

        return $this->senseWords;

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
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }

    public function getBase()
    {

        return $this->base;

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
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * @param mixed $pos
     *
     * @return self
     */
    public function setPos($pos)
    {
        $this->pos = $pos;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     *
     * @return self
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMisc()
    {
        return $this->misc;
    }

    /**
     * @param mixed $misc
     *
     * @return self
     */
    public function setMisc($misc)
    {
        $this->misc = $misc;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDial()
    {
        return $this->dial;
    }

    /**
     * @param mixed $dial
     *
     * @return self
     */
    public function setDial($dial)
    {
        $this->dial = $dial;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param mixed $info
     *
     * @return self
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }
}
