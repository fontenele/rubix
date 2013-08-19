<?php

namespace FS\View\Helper;

//use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormSubmit as ZFFormSubmit;

class FormSubmit extends ZFFormSubmit {

    /*public function render(ElementInterface $element) {
        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(
                    sprintf(
                            '%s requires that the element has an assigned name; none discovered', __METHOD__
                    )
            );
        }

        $attributes = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['type'] = $this->getType($element);
        $attributes['value'] = $element->getValue();

        return sprintf(
                        '<div class="control-group"><input %s /%s </div>', $this->createAttributesString($attributes), $this->getInlineClosingBracket()
        );
    }*/

}

?>
