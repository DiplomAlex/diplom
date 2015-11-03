<?php

class App_Form extends Zend_Form
{

    const ANSWER_YES    = 'yes';
    const ANSWER_NO     = 'no';
    const ANSWER_SUBMIT = 'submit';
    const ANSWER_CANCEL = 'cancel';
    
    /**
     * (non-PHPdoc)
     * @see Zend_Form::$_disableLoadDefaultDecorators
     */
    protected $_disableLoadDefaultDecorators = TRUE;

    /**
     * (non-PHPdoc)
     * @see Zend_Form::$_elementDecorators
     */
    protected $_elementDecorators = array(
        'ViewHelper',
    );

    /**
     * special form message
     * @var string
     */
    protected $_text = NULL;

    /**
     * @param array $options
     */
    public function __construct($options = null)
    {
        $this->addPrefixPath('App_Form_', APPLICATION_PATH .'/library/App/Form');
        $this->addElementPrefixPath('App_Form_Element_', APPLICATION_PATH .'/library/App/Form/Element');
        parent::__construct($options);
    }


    /**
     * translate all errors of element and implode them with <br/>
     * @return string
     */
    public function getElementErrorsFormatted($elName, $errors = NULL, $translate = TRUE)
    {
        if (is_array($errors)) {
            foreach ($errors as $key=>$value) {
                $text = $elName . '.' . $value;
                if ($translate) {
                    $text = $this->getTranslator()->_($text);
                }
                $errors[$key] = $text;
            }
            $html = implode('<br/>', $errors);
        }
        else {
            $html = $errors;
        }
        return $html;
    }


    /**
     * @param string
     */
    public function setText($txt)
    {
        $this->_text = $txt;
        return $this;
    }


    /**
     * returns form message text
     * @return string
     */
    public function getText()
    {
        return $this->_text;
    }


    /**
     * recieve "answer" - submit button user have pressed
     * @return string
     */
    public function getAnswer() {
        if ($this->getMethod() == Zend_Form::METHOD_GET) {
            $set = $_GET;
        }
        else {
            $set = $_POST;
        }
        foreach ($this->getElements() as $el) {
            if (( ! empty($set[$el->getName()]))
               AND ($el instanceof Zend_Form_Element_Submit)) {
                    return $el->getName();
            }
        }
        return FALSE;
    }
    
    /**
     * checks if form answer form positive - yes or submit
     * @return bool
     */
    public function isAnswerPositive()
    {
        $negatives = array(self::ANSWER_NO, self::ANSWER_CANCEL);
        $result =  ! in_array($this->getAnswer(), $negatives);
        return $result;
    }

    /**
     * (non-PHPdoc)
     * @see Zend_Form::addElement()
     */
    public function addElement($element, $name = null, $options = null)
    {
        if ((is_string($element) AND $name=='submit') OR (($element instanceof Zend_Form_Element) AND $element->getName()=='submit')) {
            throw new Zend_Form_Exception('element with name "submit" cannot be added to form ('.get_class($this).') - use another element\'s name');
        }
        return parent::addElement($element, $name, $options);
    }


}