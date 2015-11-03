<?php

class App_Rest_Server extends Zend_Rest_Server
{

    protected function _handleStruct($struct)
    {
        $dom = new DOMDocument('1.0', $this->getEncoding());
        $root = $dom->createElement('rest');
        $response = $dom->createElement('response');
        $method = $response;

        $dom->appendChild($root);
        $root->appendChild($response);

        $this->_structValue($struct, $dom, $method);

        $struct = (array)$struct;
        if (!isset($struct['status'])) {
            $status = $dom->createElement('status', 'success');
            $root->appendChild($status);
        }

        return $dom->saveXML();
    }

    /**
     * Recursively iterate through a struct
     *
     * Recursively iterates through an associative array or object's properties
     * to build XML response.
     *
     * @param mixed $struct
     * @param DOMDocument $dom
     * @param DOMElement $parent
     * @param string|null $keyName
     * @return void
     */
    protected function _structValue($struct, DOMDocument $dom, DOMElement $parent, $keyName = null)
    {
        $struct = (array) $struct;

        foreach ($struct as $key => $value) {
            if ($value === false) {
                $value = 0;
            } elseif ($value === true) {
                $value = 1;
            }

            if (ctype_digit((string)$key)) {
                $key = $keyName;
            }

            if (is_array($value) || is_object($value)) {
                if (ctype_digit((string) key($value))) {
                    $element = $dom->createElement($key);
                    $this->_structValue($value, $dom, $parent, $key);
                } else {
                    $element = $dom->createElement($key);
                    $this->_structValue($value, $dom, $element);
                }
            } else {
                $element = $dom->createElement($key);
                $element->appendChild($dom->createTextNode($value));
            }

            if ($element->hasChildNodes()) {
                $parent->appendChild($element);
            }
        }
    }

    protected function _handleScalar($value)
    {
        $dom = new DOMDocument('1.0', $this->getEncoding());
        $xml = $dom->createElement('rest');
        $methodNode = $xml;
        $dom->appendChild($xml);

        if ($value === false) {
            $value = 0;
        } elseif ($value === true) {
            $value = 1;
        }

        if (isset($value)) {
            $element = $dom->createElement('response');
            $element->appendChild($dom->createTextNode($value));
            $methodNode->appendChild($element);
        } else {
            $methodNode->appendChild($dom->createElement('response'));
        }

        $methodNode->appendChild($dom->createElement('status', 'success'));

        return $dom->saveXML();
    }

    public function fault($exception = null, $code = null)
    {
        $function = $method = 'rest';
        $dom = new DOMDocument('1.0', $this->getEncoding());
        $xml = $dom->createElement($method);
        $xmlMethod = $xml;
        $dom->appendChild($xml);

        $xmlResponse = $dom->createElement('response');
        $xmlMethod->appendChild($xmlResponse);

        if ($exception instanceof Exception) {
            $element = $dom->createElement('message');
            $element->appendChild($dom->createTextNode($exception->getMessage()));
            $xmlResponse->appendChild($element);
            $code = $exception->getCode();
        } elseif (($exception !== null) || 'rest' == $function) {
            $xmlResponse->appendChild($dom->createElement('message', 'An unknown error occured. Please try again.'));
        } else {
            $xmlResponse->appendChild($dom->createElement('message', 'Call to ' . $method . ' failed.'));
        }

        $xmlMethod->appendChild($xmlResponse);
        $xmlMethod->appendChild($dom->createElement('status', 'failed'));

        // Headers to send
        if ($code === null || (404 != $code)) {
            $this->_headers[] = 'HTTP/1.0 400 Bad Request';
        } else {
            $this->_headers[] = 'HTTP/1.0 404 File Not Found';
        }

        return $dom;
    }

}