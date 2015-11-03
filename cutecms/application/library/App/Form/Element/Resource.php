<?php

class App_Form_Element_Resource extends Zend_Form_Element_File {

    public $helper = 'formResource';

    protected $_value = NULL;

	public function setValue($val)
    {
        $this->_value = $val;
    }

	public function getValue()
    {
		$val =  parent::getValue();
        if ($val === NULL) {
            $val = $this->_value;
        }
        return $val;
	}



    /**
     * Render form element
     * disable checking for decorator interface
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            $this->setView($view);
        }

        $content = '';
        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }
        return $content;
    }

}