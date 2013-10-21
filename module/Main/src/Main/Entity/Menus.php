<?php

namespace Main\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gerencial.menus
 *
 * @ORM\Table(name="gerencial.menus")
 * @ORM\Entity
 */
class Menus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="int_cod", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="gerencial.menus_int_cod_seq", allocationSize=1, initialValue=1)
     */
    private $intCod;

    /**
     * @var string
     *
     * @ORM\Column(name="str_nome", type="string", length=30, nullable=false)
     */
    private $strNome;



    /**
     * Get intCod
     *
     * @return integer 
     */
    public function getIntCod()
    {
        return $this->intCod;
    }

    /**
     * Set strNome
     *
     * @param string $strNome
     * @return Menus
     */
    public function setStrNome($strNome)
    {
        $this->strNome = $strNome;

        return $this;
    }

    /**
     * Get strNome
     *
     * @return string 
     */
    public function getStrNome()
    {
        return $this->strNome;
    }
}
