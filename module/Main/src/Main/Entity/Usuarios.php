<?php

namespace Main\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rubix\Mvc\Entity;

/**
 * Gerencial.usuarios
 *
 * @ORM\Table(name="gerencial.usuarios", indexes={@ORM\Index(name="IDX_F5284DE580F9CC2A", columns={"int_perfil"})})
 * @ORM\Entity
 */
class Usuarios extends Entity {

    /**
     * @var integer
     *
     * @ORM\Column(name="int_cod", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="gerencial.usuarios_int_cod_seq", allocationSize=1, initialValue=1)
     */
    private $intCod;

    /**
     * @var string
     *
     * @ORM\Column(name="str_login", type="string", length=12, nullable=false)
     */
    private $strLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="str_senha", type="string", length=32, nullable=false)
     */
    private $strSenha;

    /**
     * @var string
     *
     * @ORM\Column(name="str_nome", type="string", length=200, nullable=false)
     */
    private $strNome;

    /**
     * @var string
     *
     * @ORM\Column(name="str_email", type="string", length=100, nullable=true)
     */
    private $strEmail;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dat_ultimo_login", type="datetime", nullable=true)
     */
    private $datUltimoLogin = 'now()';

    /**
     * @var \Main\Entity\Perfis
     *
     * @ORM\ManyToOne(targetEntity="Main\Entity\Perfis")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="int_perfil", referencedColumnName="int_cod")
     * })
     */
    private $intPerfil;

    /**
     * Get intCod
     *
     * @return integer
     */
    public function getIntCod() {
        return $this->intCod;
    }

    /**
     * Set strLogin
     *
     * @param string $strLogin
     * @return Usuarios
     */
    public function setStrLogin($strLogin) {
        $this->strLogin = $strLogin;

        return $this;
    }

    /**
     * Get strLogin
     *
     * @return string
     */
    public function getStrLogin() {
        return $this->strLogin;
    }

    /**
     * Set strSenha
     *
     * @param string $strSenha
     * @return Usuarios
     */
    public function setStrSenha($strSenha) {
        $this->strSenha = $strSenha;

        return $this;
    }

    /**
     * Get strSenha
     *
     * @return string
     */
    public function getStrSenha() {
        return $this->strSenha;
    }

    /**
     * Set strNome
     *
     * @param string $strNome
     * @return Usuarios
     */
    public function setStrNome($strNome) {
        $this->strNome = $strNome;

        return $this;
    }

    /**
     * Get strNome
     *
     * @return string
     */
    public function getStrNome() {
        return $this->strNome;
    }

    /**
     * Set strEmail
     *
     * @param string $strEmail
     * @return Usuarios
     */
    public function setStrEmail($strEmail) {
        $this->strEmail = $strEmail;

        return $this;
    }

    /**
     * Get strEmail
     *
     * @return string
     */
    public function getStrEmail() {
        return $this->strEmail;
    }

    /**
     * Set datUltimoLogin
     *
     * @param \DateTime $datUltimoLogin
     * @return Usuarios
     */
    public function setDatUltimoLogin($datUltimoLogin) {
        $this->datUltimoLogin = $datUltimoLogin;

        return $this;
    }

    /**
     * Get datUltimoLogin
     *
     * @return \DateTime
     */
    public function getDatUltimoLogin() {
        return $this->datUltimoLogin;
    }

    /**
     * Set intPerfil
     *
     * @param \Main\Entity\Perfis $intPerfil
     * @return Usuarios
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
