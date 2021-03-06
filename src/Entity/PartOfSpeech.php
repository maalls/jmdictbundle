<?php

namespace Maalls\JMDictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(options={"charset"="utf8mb4", "collate"="utf8mb4_bin"},
 *   uniqueConstraints={
 *        @ORM\UniqueConstraint(name="value_sub", 
 *            columns={"value", "sub_category"})
 *    }
 * )
 * @ORM\Entity()
 */
class PartOfSpeech
{
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=16)
     */
    private $value;


    /**
     * @ORM\Column(type="string", length=128)
     */
    private $sub_category = '';


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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubCategory()
    {
        return $this->sub_category;
    }

    /**
     * @param mixed $sub_category
     *
     * @return self
     */
    public function setSubCategory($sub_category)
    {
        $this->sub_category = $sub_category;

        return $this;
    }

}




