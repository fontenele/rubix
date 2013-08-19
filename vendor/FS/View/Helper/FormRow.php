<?php

namespace FS\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormRow as ZFFormRow;

class FormRow extends ZFFormRow {

    public function render(ElementInterface $element) {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();
        $elementHelper = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();

        $label = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();
        $elementErrors = $elementErrorsHelper->render($element);
        $type = $element->getAttribute('type');

        // Does this element have errors ?
        if (!empty($elementErrors) && !empty($inputErrorClass)) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;

            $element->setAttribute('class', $classAttributes);
        }

        if($element->getAttribute('type') == 'image') {
            $element->setAttribute('type', 'hidden');
            $element->setAttribute('id', $element->getAttribute('name'));
            $img = $element->getValue() ? APPLICATION_URL . 'upload/galeria-fotos/' . $element->getValue() : 'http://www.placehold.it/770x270/EFEFEF/AAAAAA&text=no+image';

            $elementString = $elementHelper->render($element);
            $elementString = <<< HTML
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-new thumbnail" style="width: 770px; height: 270px;">
                            <img id="img_{$element->getAttribute('name')}" src="{$img}" />
                        </div>
                        <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 770px; max-height: 270px; line-height: 20px;"></div>
                        <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new" data-ref="{$element->getAttribute('name')}">Selecione</span>
                                <span class="fileupload-exists">Alterar</span>
                                {$elementString}
                            </span>
                            <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remover</a>
                        </div>
                    </div>

HTML;
        }else{
            $elementString = $elementHelper->render($element);
        }

        if (isset($label) && '' !== $label) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                        $label, $this->getTranslatorTextDomain()
                );
            }

            $label = $escapeHtmlHelper($label);
            $labelAttributes = $element->getLabelAttributes();

            if (empty($labelAttributes)) {
                $labelAttributes = $this->labelAttributes;
            }

            if ($element->getAttribute('required')) {
                $labelAttributes['required'] = '';
            }

            $markup = '<div class="control-group">';

            if ($type === 'multi_checkbox' || $type === 'radio') {
                $markup.= sprintf(
                        '<fieldset><legend>%s</legend>%s</fieldset>', $label, $elementString);
            } else {
                if ($element->hasAttribute('id')) {
                    $labelOpen = '';
                    $labelClose = '';
                    $label = $labelHelper($element);
                } else {
                    $labelOpen = $labelHelper->openTag($labelAttributes);
                    $labelClose = $labelHelper->closeTag();
                }
                if ($label !== '' && !$element->hasAttribute('id')) {
                    //$label = '<span>' . $label . '</span>';
                }

                switch ($this->labelPosition) {
                    case self::LABEL_PREPEND:
                        $markup.= $labelOpen . $label . $labelClose . '<div class="controls">' . $elementString . '</div>';
                        break;
                    case self::LABEL_APPEND:
                    default:
                        $markup.= $labelOpen . $label . $labelClose . '<div class="controls">' . $elementString . '</div>';
                        break;
                }
            }

            if ($this->renderErrors) {
                $markup.= $elementErrors;
            }

            $markup.= '</div>';
        } else {
            if ($this->renderErrors) {
                $markup = $elementString . $elementErrors;
            } else {
                $markup = $elementString;
            }
        }

        return $markup;
    }

}

?>
