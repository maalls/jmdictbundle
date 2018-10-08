<?php

namespace Maalls\JMDictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(options={"charset"="utf8mb4", "collate"="utf8mb4_bin"},
 *   uniqueConstraints={
 *        @ORM\UniqueConstraint(name="edict_part_of_speech", 
 *            columns={"part_of_speech_id", "edict_pos"})
 *    }
 *)
 * @ORM\Entity()
 */
class EdictPos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $edict_pos;

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
    public function getEdictPos()
    {
        return $this->edict_pos;
    }

    /**
     * @param mixed $edict_pos
     *
     * @return self
     */
    public function setEdictPos($edict_pos)
    {
        $this->edict_pos = $edict_pos;

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
