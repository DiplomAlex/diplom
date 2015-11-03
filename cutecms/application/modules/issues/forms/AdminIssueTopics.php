<?php

class Issues_Form_AdminIssueTopics extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');

        $attrs = $this ->createElement('flexiGrid', 'topics')
                       ->setLabel(/*$this->getTranslator()->_('Topics')*/'')
                       ->setRequired(FALSE)
                       ->setAttribs(array(
                            'width' => 452,
                            'height' => 400,
                            'colModel' => $this->_prepareColModel(),
                            'url' => $this->_prepareUrlGet(),
                            'urlAdd' => $this->_prepareUrlAdd(),
                            'urlEdit' => $this->_prepareUrlEdit(),
                            'urlDelete' => $this->_prepareUrlDelete(),
                            'editForm' => new Issues_Form_AdminIssueTopicEdit,
                       ))
                       ;
        $this->addElement($attrs);



        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));

    }


    protected function _prepareColModel()
    {
        return
            array(
                array('name' =>'text', 'display'=>$this->getTranslator()->_(''),
                      'width'=>'400', 'sortable'=>TRUE, 'align'=>'left'),
            );
    }



    protected function _prepareUrlGet()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-topic',    'admin-issue', 'issues');
    }

    protected function _prepareUrlAdd()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-new-topic',    'admin-issue', 'issues');
    }

    protected function _prepareUrlEdit()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-edit-topic',   'admin-issue', 'issues');
    }

    protected function _prepareUrlDelete()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-delete-topic', 'admin-issue', 'issues');
    }


}

