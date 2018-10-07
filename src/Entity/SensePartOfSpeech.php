<?php

namespace Maalls\JMDictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(options={"charset"="utf8mb4", "collate"="utf8mb4_bin"},
 *   uniqueConstraints={
 *        @ORM\UniqueConstraint(name="sense_part_of_speech", 
 *            columns={"sense_id", "jdic_pos"})
 *    }
 *)
 * @ORM\Entity()
 */
class SensePartOfSpeech
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    // add your own fields

    /**
     * @ORM\ManyToOne(targetEntity="Sense", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $sense;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $jdic_pos;

    /**
     * @ORM\ManyToOne(targetEntity="PartOfSpeech", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $partOfSpeech;


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
    public function getJdicPos()
    {
        return $this->jdic_pos;
    }

    /**
     * @param mixed $jdic_pos
     *
     * @return self
     */
    public function setJdicPos($jdic_pos)
    {
        $this->jdic_pos = $jdic_pos;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPartOfSpeech()
    {
        return $this->partOfSpeech;
    }

    /**
     * @param mixed $partOfSpeech
     *
     * @return self
     */
    public function setPartOfSpeech($partOfSpeech)
    {
        $this->partOfSpeech = $partOfSpeech;

        return $this;
    }
}
