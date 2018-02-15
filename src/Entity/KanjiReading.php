<?php

namespace Maalls\JMDictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(options={"charset"="utf8mb4", "collate"="utf8mb4_bin"})
 * @ORM\Entity(repositoryClass="Maalls\JMDictBundle\Repository\KanjiReadingRepository")
 */
class KanjiReading
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    // add your own fields


    /**
     * @ORM\ManyToOne(targetEntity="Word")
     * @ORM\JoinColumn(name="kanji_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $kanji;

    /**
     * @ORM\ManyToOne(targetEntity="Word")
     * @ORM\JoinColumn(name="reading_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $reading;

    


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
    public function getKanji()
    {
        return $this->kanji;
    }

    /**
     * @param mixed $kanji
     *
     * @return self
     */
    public function setKanji($kanji)
    {
        $this->kanji = $kanji;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReading()
    {
        return $this->reading;
    }

    /**
     * @param mixed $reading
     *
     * @return self
     */
    public function setReading($reading)
    {
        $this->reading = $reading;

        return $this;
    }
}
