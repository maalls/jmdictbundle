<?php

namespace Maalls\JMDictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Maalls\HeisigBundle\Entity\Heisig;
/**
 * @ORM\Table(
 *   options={"charset"="utf8mb4", "collate"="utf8mb4_bin"},
 *   uniqueConstraints={
 *        @ORM\UniqueConstraint(name="word_kanji", 
 *            columns={"word_id", "heisig_id"})
 *    }
 *)
 * @ORM\Entity(repositoryClass="Maalls\JMDictBundle\Repository\SenseRepository")
 */
class WordKanji
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Word", inversedBy="wordKanjis")
     * @ORM\JoinColumn(name="word_id", referencedColumnName="id")
     */
    private $word;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $kanji;


    /**
     * @ORM\ManyToOne(targetEntity="Maalls\HeisigBundle\Entity\Heisig")
     * @ORM\JoinColumn(name="heisig_id", referencedColumnName="id")
     */
    private $heisig;


    public function __toString()
    {

        return $this->getHeisig()->getKanji() . ":" . $this->getHeisig()->getKeyword();

    }


    public function setKanji($kanji)
    {

        $this->kanji = $kanji;

    }

    public function getKanji()
    {

        return $this->kanji;

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
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @param mixed $word
     *
     * @return self
     */
    public function setWord($word)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeisig()
    {
        return $this->heisig;
    }

    /**
     * @param mixed $heisig
     *
     * @return self
     */
    public function setHeisig($heisig)
    {
        $this->heisig = $heisig;

        return $this;
    }
}