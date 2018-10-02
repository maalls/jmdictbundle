<?php

namespace Maalls\JMDictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Maalls\HeisigBundle\Entity\Heisig;
/**
 * @ORM\Table(
 *   options={"charset"="utf8mb4", "collate"="utf8mb4_bin"},
 *   uniqueConstraints={
 *        @ORM\UniqueConstraint(name="word_reading", 
 *            columns={"word_id", "reading_id"})
 *    }
 *)
 * @ORM\Entity(repositoryClass="Maalls\JMDictBundle\Repository\WordReadingRepository")
 */
class WordReading
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Word", cascade={"persist"})
     * @ORM\JoinColumn(name="word_id", referencedColumnName="id")
     */
    private $word;

    /**
     * @ORM\ManyToOne(targetEntity="Word", cascade={"persist"})
     * @ORM\JoinColumn(name="reading_id", referencedColumnName="id")
     */
    private $reading;

    /**
     * @ORM\Column(type="integer")
     */
    private $code;




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

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     *
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}