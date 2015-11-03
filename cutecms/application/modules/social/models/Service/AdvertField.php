<?php


class Social_Model_Service_AdvertField extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Mapper_Interface' => 'Social_Model_Mapper_XML_AdvertField',
        'Model_Object_Interface' => 'Social_Model_Object_AdvertField',
        'Model_Collection_Interface' => 'Social_Model_Collection_AdvertField',
    );


    /**
     * @param string
     * @return Model_Collection_Interface
     */
    public function getCollectionFromXML($xml)
    {
        return $this->getMapper()->makeSimpleCollection($xml);
    }

    public function getObjectFromXML($xml)
    {
        return $this->getMapper()->makeSimpleObject($xml);
    }

}
