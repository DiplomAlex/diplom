<?php

class Shop_Form_Filter extends App_Form
{
    
    public function init()
    {
        $this->addElement('text', 'filter_price_min', array(
            'attribs' => array(
                'id' => 'min_price',
                'value' => $this->getTranslator()->_('от'),
                'onmouseout' => 'if ((this!=document.activeElement)&&(this.value=="")) this.value="'.$this->getTranslator()->_('от').'"',
                'onmouseover' => 'if (this.value == "'.$this->getTranslator()->_('от').'") this.value = "";'
            ),
        ));
        
        $this->addElement('text', 'filter_price_max', array(
            'attribs' => array(
                'id' => 'max_price',
                'value' => $this->getTranslator()->_('до'),
                'onmouseout' => 'if ((this!=document.activeElement)&&(this.value=="")) this.value="'.$this->getTranslator()->_('до').'"',
                'onmouseover' => 'if (this.value == "'.$this->getTranslator()->_('до').'") this.value = "";'
            ),
        ));

        $this->addElement('text', 'filter_manufacturer', array(
            'attribs' => array(
                'id' => 'mark',
            ),
        ));
        
    }
    
}