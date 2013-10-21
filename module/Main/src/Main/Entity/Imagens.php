<?php

namespace Main\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Imagens
 *
 * @ORM\Table(name="imagens", indexes={@ORM\Index(name="IDX_49E534EF11703102", columns={"int_usuario"}), @ORM\Index(name="IDX_49E534EFE98CD315", columns={"int_categoria"})})
 * @ORM\Entity
 */
class Imagens
{
    /**
     * @var integer
     *
     * @ORM\Column(name="int_cod", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="imagens_int_cod_seq", allocationSize=1, initialValue=1)
     */
    private $intCod;

    /**
     * @var string
     *
     * @ORM\Column(name="str_nome_original", type="string", length=500, nullable=false)
     */
    private $strNomeOriginal;

    /**
     * @var integer
     *
     * @ORM\Column(name="int_tamanho", type="integer", nullable=false)
     */
    private $intTamanho;

    /**
     * @var string
     *
     * @ORM\Column(name="str_nome", type="string", length=100, nullable=false)
     */
    private $strNome;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dat_cadastro", type="datetime", nullable=true)
     */
    private $datCadastro = 'now()';

    /**
     * @var \Main\Entity\Gerencial.usuarios
     *
     * @ORM\ManyToOne(targetEntity="Main\Entity\Usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="int_usuario", referencedColumnName="int_cod")
     * })
     */
    private $intUsuario;

    /**
     * @var \Main\Entity\CategoriasImagens
     *
     * @ORM\ManyToOne(targetEntity="Main\Entity\CategoriasImagens")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="int_categoria", referencedColumnName="int_cod")
     * })
     */
    private $intCategoria;



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
     * Set strNomeOriginal
     *
     * @param string $strNomeOriginal
     * @return Imagens
     */
    public function setStrNomeOriginal($strNomeOriginal)
    {
        $this->strNomeOriginal = $strNomeOriginal;

        return $this;
    }

    /**
     * Get strNomeOriginal
     *
     * @return string 
     */
    public function getStrNomeOriginal()
    {
        return $this->strNomeOriginal;
    }

    /**
     * Set intTamanho
     *
     * @param integer $intTamanho
     * @return Imagens
     */
    public function setIntTamanho($intTamanho)
    {
        $this->intTamanho = $intTamanho;

        return $this;
    }

    /**
     * Get intTamanho
     *
     * @return integer 
     */
    public function getIntTamanho()
    {
        return $this->intTamanho;
    }

    /**
     * Set strNome
     *
     * @param string $strNome
     * @return Imagens
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
     * Set datCadastro
     *
     * @param \DateTime $datCadastro
     * @return Imagens
     */
    public function setDatCadastro($datCadastro)
    {
        $this->datCadastro = $datCadastro;

        return $this;
    }

    /**
     * Get datCadastro
     *
     * @return \DateTime 
     */
    public function getDatCadastro()
    {
        return $this->datCadastro;
    }

    /**
     * Set intUsuario
     *
     * @param \Main\Entity\Usuarios $intUsuario
     * @return Imagens
     */
    public function setIntUsuario(\Main\Entity\Usuarios $intUsuario = null)
    {
        $this->intUsuario = $intUsuario;

        return $this;
    }

    /**
     * Get intUsuario
     *
     * @return \Main\Entity\Usuarios 
     */
    public function getIntUsuario()
    {
        return $this->intUsuario;
    }

    /**
     * Set intCategoria
     *
     * @param \Main\Entity\CategoriasImagens $intCategoria
     * @return Imagens
     */
    public function setIntCategoria(\Main\Entity\CategoriasImagens $intCategoria = null)
    {
        $this->intCategoria = $intCategoria;

        return $this;
    }

    /**
     * Get intCategoria
     *
     * @return \Main\Entity\CategoriasImagens 
     */
    public function getIntCategoria()
    {
        return $this->intCategoria;
    }
}
