<?php

namespace Main\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoriasImagens
 *
 * @ORM\Table(name="categorias_imagens", indexes={@ORM\Index(name="IDX_68044535E2090E5", columns={"int_cod_pai"})})
 * @ORM\Entity
 */
class CategoriasImagens
{
    /**
     * @var integer
     *
     * @ORM\Column(name="int_cod", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="categorias_imagens_int_cod_seq", allocationSize=1, initialValue=1)
     */
    private $intCod;

    /**
     * @var string
     *
     * @ORM\Column(name="str_nome", type="string", length=100, nullable=false)
     */
    private $strNome;

    /**
     * @var \Main\Entity\CategoriasImagens
     *
     * @ORM\ManyToOne(targetEntity="Main\Entity\CategoriasImagens")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="int_cod_pai", referencedColumnName="int_cod")
     * })
     */
    private $intCodPai;



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
     * @return CategoriasImagens
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

    /**
     * Set intCodPai
     *
     * @param \Main\Entity\CategoriasImagens $intCodPai
     * @return CategoriasImagens
     */
    public function setIntCodPai(\Main\Entity\CategoriasImagens $intCodPai = null)
    {
        $this->intCodPai = $intCodPai;

        return $this;
    }

    /**
     * Get intCodPai
     *
     * @return \Main\Entity\CategoriasImagens 
     */
    public function getIntCodPai()
    {
        return $this->intCodPai;
    }
}
