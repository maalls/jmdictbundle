<?php

namespace Maalls\JMDictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(options={"charset"="utf8mb4", "collate"="utf8mb4_bin"}, indexes={@ORM\Index(name="value_idx", columns={"value"})})
 * @ORM\Entity(repositoryClass="Maalls\JMDictBundle\Repository\WordRepository")
 */
class Word
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    // add your own fields

    /**
     * @ORM\ManyToOne(targetEntity="Base")
     * @ORM\JoinColumn(name="base_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $base;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $info;

    /**
     * @ORM\Column(type="boolean")
     */
    private $no_kanji;

    /**
     * @ORM\Column(type="smallint")
     */
    private $news_level;

    /**
     * @ORM\Column(type="smallint")
     */
    private $ichi_level;

    /**
     * @ORM\Column(type="smallint")
     */
    private $spe_level;

    /**
     * @ORM\Column(type="smallint")
     */
    private $gai_level;

    /**
     * @ORM\Column(type="smallint")
     */
    private $frequency_level;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $value;

    /**
     * @ORM\OneToMany(targetEntity="SenseWord", mappedBy="word")
     */
    private $senseWords;

    /**
     * @ORM\OneToMany(targetEntity="KanjiReading", mappedBy="kanji")
     */
    private $kanjiReadings;


    public function __construct()
    {

        $this->senseWords = new Doctrine\Common\Collections\ArrayCollection();
        $this->kanjiReadings = new Doctrine\Common\Collections\ArrayCollection();

    }

    public function getKanjiReadings()
    {

        return $this->kanjiReadings;

    }

    public function getSenseWords()
    {

        return $this->senseWords;

    }


    public function getValue() 
    {

        return $this->value;

    }

    public function setValue($value)
    {

        $this->value = $value;

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
    public function getBase()
    {
        return $this->base;
    }

    /**
     * @param mixed $base
     *
     * @return self
     */
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

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

    /**
     * @return mixed
     */
    public function getNoKanji()
    {
        return $this->no_kanji;
    }

    /**
     * @param mixed $no_kanji
     *
     * @return self
     */
    public function setNoKanji($no_kanji)
    {
        $this->no_kanji = $no_kanji;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNewsLevel()
    {
        return $this->news_level;
    }

    /**
     * @param mixed $news_level
     *
     * @return self
     */
    public function setNewsLevel($news_level)
    {
        $this->news_level = $news_level;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIchiLevel()
    {
        return $this->ichi_level;
    }

    /**
     * @param mixed $ichi_level
     *
     * @return self
     */
    public function setIchiLevel($ichi_level)
    {
        $this->ichi_level = $ichi_level;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSpeLevel()
    {
        return $this->spe_level;
    }

    /**
     * @param mixed $spec_level
     *
     * @return self
     */
    public function setSpeLevel($spe_level)
    {
        $this->spe_level = $spe_level;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGaiLevel()
    {
        return $this->gai_level;
    }

    /**
     * @param mixed $gai_level
     *
     * @return self
     */
    public function setGaiLevel($gai_level)
    {
        $this->gai_level = $gai_level;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrequencyLevel()
    {
        return $this->frequency_level;
    }

    /**
     * @param mixed $frequency_level
     *
     * @return self
     */
    public function setFrequencyLevel($frequency_level)
    {
        $this->frequency_level = $frequency_level;

        return $this;
    }
}
