<?php

namespace Main\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Rubix\Mvc\Entity;

/**
 * Acessos
 *
 * @ORM\Table(name="gerencial.acessos", indexes={@ORM\Index(name="IDX_61CDE12C80F9CC2A", columns={"int_perfil"})})
 * @ORM\Entity
 */
class Acessos extends Entity {

    /**
     * @var integer
     *
     * @ORM\Column(name="int_cod", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="gerencial.acessos_int_cod_seq", allocationSize=1, initialValue=1)
     */
    private $intCod;

    /**
     * @var string
     *
     * @ORM\Column(name="str_nome_acesso", type="string", length=300, nullable=false)
     */
    private $strNomeAcesso;

    /**
     * @var \Main\Entity\Gerencial.perfis
     *
     * @ORM\ManyToOne(targetEntity="Main\Entity\Perfis")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="int_perfil", referencedColumnName="int_cod")
     * })
     */
    private $intPerfil;

    /**
     * Set intCod
     *
     * @param integer $intCod
     * @return Perfis
     */
    public function setIntCod($intCod = null) {
        $this->intCod = $intCod;
        return $this;
    }

    /**
     * Get intCod
     *
     * @return integer 
     */
    public function getIntCod() {
        return $this->intCod;
    }

    /**
     * Set strNomeAcesso
     *
     * @param string $strNomeAcesso
     * @return Acessos
     */
    public function setStrNomeAcesso($strNomeAcesso) {
        $this->strNomeAcesso = $strNomeAcesso;

        return $this;
    }

    /**
     * Get strNomeAcesso
     *
     * @return string 
     */
    public function getStrNomeAcesso() {
        return $this->strNomeAcesso;
    }

    /**
     * Set intPerfil
     *
     * @param \Main\Entity\Perfis $intPerfil
     * @return Acessos
     */
    public function setIntPerfil(\Main\Entity\Perfis $intPerfil = null) {
        $this->intPerfil = $intPerfil;

        return $this;
    }

    /**
     * Get intPerfil
     *
     * @return \Main\Entity\Perfis 
     */
    public function getIntPerfil() {
        return $this->intPerfil;
    }

}
