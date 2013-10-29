<?php

namespace Main\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
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

    public function exchangeArray($data) {
        $this->intCod = (isset($data['intCod'])) ? $data['intCod'] : null;
        $this->strNome = (isset($data['strNome'])) ? $data['strNome'] : null;
        $this->intSituacao = (isset($data['intSituacao'])) ? $data['intSituacao'] : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'intCod',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        ),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'strNome',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 1,
                                    'max' => 100,
                                ),
                            ),
                        ),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'intSituacao',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
