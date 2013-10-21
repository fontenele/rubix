<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Rubix\Soap;

use Zend\Soap\Wsdl as WsdlBase;
use DOMDocument;

class Wsdl extends WsdlBase {

    /**
     * Get the wsdl XML document with all namespaces and required attributes
     *
     * @param  string $uri
     * @param  string $name
     * @return DOMDocument
     */
    protected function getDOMDocument($name, $uri = null) {
        $dom = new DOMDocument();

        // @todo new option for debug mode ?
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->resolveExternals = false;
        $dom->encoding = 'UTF-8';
        $dom->substituteEntities = false;

        $definitions = $dom->createElementNS(self::WSDL_NS_URI, 'wsdl:definitions');
        $dom->appendChild($definitions);

        $uri = $this->sanitizeUri($uri);
        $this->setAttributeWithSanitization($definitions, 'name', $name);
        $this->setAttributeWithSanitization($definitions, 'targetNamespace', $uri);

        $definitions->setAttributeNS(self::XML_NS_URI, 'xmlns:' . self::WSDL_NS, self::WSDL_NS_URI);
        $definitions->setAttributeNS(self::XML_NS_URI, 'xmlns:' . self::TYPES_NS, $uri);
        $definitions->setAttributeNS(self::XML_NS_URI, 'xmlns:' . self::SOAP_11_NS, self::SOAP_11_NS_URI);
        $definitions->setAttributeNS(self::XML_NS_URI, 'xmlns:' . self::XSD_NS, self::XSD_NS_URI);
        $definitions->setAttributeNS(self::XML_NS_URI, 'xmlns:' . self::SOAP_ENC_NS, self::SOAP_ENC_URI);
        $definitions->setAttributeNS(self::XML_NS_URI, 'xmlns:' . self::SOAP_12_NS, self::SOAP_12_NS_URI);

        return $dom;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_messages message} element to the WSDL
     *
     * @param  string $messageName Name for the {@link http://www.w3.org/TR/wsdl#_messages message}
     * @param  array $parts An array of {@link http://www.w3.org/TR/wsdl#_message parts}
     *                      The array is constructed like:
     *                          'name of part' => 'part xml schema data type' or
     *                          'name of part' => array('type' => 'part xml schema type')  or
     *                          'name of part' => array('element' => 'part xml element name')
     * @return DOMElement The new message's XML_Tree_Node for use in {@link function addDocumentation}
     */
    public function addMessage($messageName, $parts)
    {
        $message = $this->dom->createElementNS(self::WSDL_NS_URI, 'message');
        $message->setAttribute('name', $messageName);

        if (count($parts) > 0) {

            foreach ($parts as $name => $type) {
                $part = $this->dom->createElementNS(self::WSDL_NS_URI, 'part');
                $message->appendChild($part);

                $nome = ($name == 'return' && isset($type['name'])) ? $type['name'] : $name;

                $part->setAttribute('name', $nome);
                if (is_array($type)) {
                    $this->arrayToAttributes($part, $type);
                } else {
                    $this->setAttributeWithSanitization($part, 'type', $type);
                }
            }
        }

        $this->wsdl->appendChild($message);
        return $message;
    }

}
