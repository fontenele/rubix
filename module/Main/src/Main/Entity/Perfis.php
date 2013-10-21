<?php

namespace Main\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rubix\Mvc\Entity;

/**
 * Perfis
 *
 * @ORM\Table(name="gerencial.perfis")
 * @ORM\Entity
 */
class Perfis extends Entity {

    /**
     * @var integer
     *
     * @ORM\Column(name="int_cod", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="gerencial.perfis_int_cod_seq", allocationSize=1, initialValue=1)
     */
    private $intCod;

    /**
     * @var string
     *
     * @ORM\Column(name="str_nome", type="string", length=100, nullable=false)
     */
    private $strNome;

    /**
     * @var integer
     *
     * @ORM\Column(name="int_situacao", type="integer", nullable=true)
     */
    private $intSituacao = '2';

    /**
     * Get intCod
     *
     * @return integer
     */
    public function getIntCod() {
        return $this->intCod;
    }

    /**
     * Set strNome
     *
     * @param string $strNome
     * @return Perfis
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
     * Set intSituacao
     *
     * @param integer $intSituacao
     * @return Perfis
     */
    public function setIntSituacao($intSituacao) {
        $this->intSituacao = $intSituacao;

        return $this;
    }

    /**
     * Get intSituacao
     *
     * @return integer
     */
    public function getIntSituacao() {
        return $this->intSituacao;
    }

}
