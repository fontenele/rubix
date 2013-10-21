<?php

namespace Main\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gerencial.menusItems
 *
 * @ORM\Table(name="gerencial.menus_items", indexes={@ORM\Index(name="IDX_F96B7BF87BEA103", columns={"int_menu"}), @ORM\Index(name="IDX_F96B7BF8E2090E5", columns={"int_cod_pai"})})
 * @ORM\Entity
 */
class MenusItems
{
    /**
     * @var integer
     *
     * @ORM\Column(name="int_cod", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="gerencial.menus_items_int_cod_seq", allocationSize=1, initialValue=1)
     */
    private $intCod;

    /**
     * @var string
     *
     * @ORM\Column(name="str_label", type="string", length=200, nullable=false)
     */
    private $strLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="str_target", type="string", length=500, nullable=true)
     */
    private $strTarget;

    /**
     * @var integer
     *
     * @ORM\Column(name="int_posicao", type="integer", nullable=true)
     */
    private $intPosicao;

    /**
     * @var string
     *
     * @ORM\Column(name="str_tipo", type="string", length=30, nullable=true)
     */
    private $strTipo = 'item';

    /**
     * @var \Main\Entity\Gerencial.menus
     *
     * @ORM\ManyToOne(targetEntity="Main\Entity\Menus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="int_menu", referencedColumnName="int_cod")
     * })
     */
    private $intMenu;

    /**
     * @var \Main\Entity\Gerencial.menusItems
     *
     * @ORM\ManyToOne(targetEntity="Main\Entity\MenusItems")
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
     * Set strLabel
     *
     * @param string $strLabel
     * @return MenusItems
     */
    public function setStrLabel($strLabel)
    {
        $this->strLabel = $strLabel;

        return $this;
    }

    /**
     * Get strLabel
     *
     * @return string 
     */
    public function getStrLabel()
    {
        return $this->strLabel;
    }

    /**
     * Set strTarget
     *
     * @param string $strTarget
     * @return MenusItems
     */
    public function setStrTarget($strTarget)
    {
        $this->strTarget = $strTarget;

        return $this;
    }

    /**
     * Get strTarget
     *
     * @return string 
     */
    public function getStrTarget()
    {
        return $this->strTarget;
    }

    /**
     * Set intPosicao
     *
     * @param integer $intPosicao
     * @return MenusItems
     */
    public function setIntPosicao($intPosicao)
    {
        $this->intPosicao = $intPosicao;

        return $this;
    }

    /**
     * Get intPosicao
     *
     * @return integer 
     */
    public function getIntPosicao()
    {
        return $this->intPosicao;
    }

    /**
     * Set strTipo
     *
     * @param string $strTipo
     * @return MenusItems
     */
    public function setStrTipo($strTipo)
    {
        $this->strTipo = $strTipo;

        return $this;
    }

    /**
     * Get strTipo
     *
     * @return string 
     */
    public function getStrTipo()
    {
        return $this->strTipo;
    }

    /**
     * Set intMenu
     *
     * @param \Main\Entity\Menus $intMenu
     * @return MenusItems
     */
    public function setIntMenu(\Main\Entity\Menus $intMenu = null)
    {
        $this->intMenu = $intMenu;

        return $this;
    }

    /**
     * Get intMenu
     *
     * @return \Main\Entity\Menus 
     */
    public function getIntMenu()
    {
        return $this->intMenu;
    }

    /**
     * Set intCodPai
     *
     * @param \Main\Entity\MenusItems $intCodPai
     * @return MenusItems
     */
    public function setIntCodPai(\Main\Entity\MenusItems $intCodPai = null)
    {
        $this->intCodPai = $intCodPai;

        return $this;
    }

    /**
     * Get intCodPai
     *
     * @return \Main\Entity\MenusItems 
     */
    public function getIntCodPai()
    {
        return $this->intCodPai;
    }
}
