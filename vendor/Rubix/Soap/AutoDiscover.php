<?php

namespace Rubix\Soap;

use Zend\Soap\AutoDiscover as AutoDiscoverBase;

class AutoDiscover extends AutoDiscoverBase {

    /**
     * Add a function to the WSDL document.
     *
     * @param  $function Reflection\AbstractFunction function to add
     * @param  $wsdl     Wsdl WSDL document
     * @param  $port     \DOMElement wsdl:portType
     * @param  $binding  \DOMElement wsdl:binding
     * @throws Exception\InvalidArgumentException
     */
    protected function _addFunctionToWsdl($function, $wsdl, $port, $binding) {
        $uri = $this->getUri();

        // We only support one prototype: the one with the maximum number of arguments
        $prototype = null;
        $maxNumArgumentsOfPrototype = -1;
        foreach ($function->getPrototypes() as $tmpPrototype) {
            $numParams = count($tmpPrototype->getParameters());
            if ($numParams > $maxNumArgumentsOfPrototype) {
                $maxNumArgumentsOfPrototype = $numParams;
                $prototype = $tmpPrototype;
            }
        }
        if ($prototype === null) {
            throw new Exception\InvalidArgumentException(sprintf(
                            'No prototypes could be found for the "%s" function', $function->getName()
            ));
        }

        $functionName = $wsdl->translateType($function->getName());

        // Add the input message (parameters)
        $args = array();
        if ($this->bindingStyle['style'] == 'document') {
            // Document style: wrap all parameters in a sequence element
            $sequence = array();
            foreach ($prototype->getParameters() as $param) {
                $sequenceElement = array(
                    'name' => $param->getName(),
                    'type' => $wsdl->getType($this->discoveryStrategy->getFunctionParameterType($param))
                );
                if ($param->isOptional()) {
                    $sequenceElement['nillable'] = 'true';
                }
                $sequence[] = $sequenceElement;
            }

            $element = array(
                'name' => $functionName,
                'sequence' => $sequence
            );

            // Add the wrapper element part, which must be named 'parameters'
            $args['parameters'] = array('element' => $wsdl->addElement($element));
        } else {
            // RPC style: add each parameter as a typed part
            foreach ($prototype->getParameters() as $param) {
                $args[$param->getName()] = array(
                    'type' => $wsdl->getType($this->discoveryStrategy->getFunctionParameterType($param))
                );
            }
        }
        $wsdl->addMessage($functionName . 'In', $args);

        $isOneWayMessage = $this->discoveryStrategy->isFunctionOneWay($function, $prototype);

        if ($isOneWayMessage == false) {
            // Add the output message (return value)
            $args = array();
            if ($this->bindingStyle['style'] == 'document') {
                // Document style: wrap the return value in a sequence element
                $sequence = array();
                if ($prototype->getReturnType() != "void") {
                    $sequence[] = array(
                        'name' => $functionName . 'Result',
                        'type' => $wsdl->getType($this->discoveryStrategy->getFunctionReturnType($function, $prototype))
                    );
                }

                $element = array(
                    'name' => $functionName . 'Response',
                    'sequence' => $sequence
                );

                // Add the wrapper element part, which must be named 'parameters'
                $args['parameters'] = array('element' => $wsdl->addElement($element));
            } elseif ($prototype->getReturnType() != "void") {
                // RPC style: add the return value as a typed part
                $args['return'] = array(
                    'name' => $prototype->return->getDescription(),
                    'type' => $wsdl->getType($this->discoveryStrategy->getFunctionReturnType($function, $prototype))
                );
            }


            $wsdl->addMessage($functionName . 'Out', $args);
        }

        // Add the portType operation
        if ($isOneWayMessage == false) {
            $portOperation = $wsdl->addPortOperation(
                    $port, $functionName, Wsdl::TYPES_NS . ':' . $functionName . 'In', Wsdl::TYPES_NS . ':' . $functionName . 'Out'
            );
        } else {
            $portOperation = $wsdl->addPortOperation(
                    $port, $functionName, Wsdl::TYPES_NS . ':' . $functionName . 'In', false
            );
        }
        $desc = $this->discoveryStrategy->getFunctionDocumentation($function);

        if (strlen($desc) > 0) {
            $wsdl->addDocumentation($portOperation, $desc);
        }

        // When using the RPC style, make sure the operation style includes a 'namespace'
        // attribute (WS-I Basic Profile 1.1 R2717)
        $operationBodyStyle = $this->operationBodyStyle;
        if ($this->bindingStyle['style'] == 'rpc' && !isset($operationBodyStyle['namespace'])) {
            $operationBodyStyle['namespace'] = '' . $uri;
        }

        // Add the binding operation
        if ($isOneWayMessage == false) {
            $operation = $wsdl->addBindingOperation($binding, $functionName, $operationBodyStyle, $operationBodyStyle);
        } else {
            $operation = $wsdl->addBindingOperation($binding, $functionName, $operationBodyStyle);
        }
        $wsdl->addSoapOperation($operation, $uri . '#' . $functionName);
    }

}