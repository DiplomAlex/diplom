<?php

class Form_Installer extends App_Form
{

    public function init()
    {

        $this->setMethod('post');

        $this->addElement('text', 'host', array(
            'label' => $this->getTranslator()->_('Имя домена'),
            'required' => TRUE,
            'attribs' => array(
                'size' => 90,
            ),
        ));

        $this->addElement('text', 'base_url', array(
            'label' => $this->getTranslator()->_('Базовый каталог'),
            'attribs' => array(
                'size' => 90,
            ),
        ));

        $this->addElement('text', 'site_name', array(
            'label' => $this->getTranslator()->_('Название сайта'),
            'required' => TRUE,
            'attribs' => array(
                'size' => 90,
            ),
        ));

        $this->addElement('text', 'support_email', array(
            'label' => $this->getTranslator()->_('Email службы поддержки сайта'),
            'required' => TRUE,
            'attribs' => array(
                'size' => 90,
            ),
        ));

        $this->addElement('hidden', 'support_name');

        $this->addElement('hidden', 'db_adapter');


        $this->addElement('text', 'db_host', array(
            'label' => $this->getTranslator()->_('Хост mysql'),
            'required' => TRUE,
            'attribs' => array(
                'size' => 90,
            ),
        ));
        $this->addElement('text', 'db_name', array(
            'label' => $this->getTranslator()->_('Имя базы данных'),
            'required' => TRUE,
            'attribs' => array(
                'size' => 90,
            ),
        ));
        $this->addElement('text', 'db_user', array(
            'label' => $this->getTranslator()->_('Пользователь БД'),
            'required' => TRUE,
            'attribs' => array(
                'size' => 90,
            ),
        ));
        $this->addElement('text', 'db_password', array(
            'label' => $this->getTranslator()->_('Пароль БД'),
            'attribs' => array(
                'size' => 90,
            ),
        ));

        $this->addElement('checkbox', 'db_import_dump', array(
            'label' => $this->getTranslator()->_('Импортировать базу'),
            'attribs' => array(
                'checked' => TRUE,
            ),
        ));


        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));


    }

}