<?php

class Model_Mapper_Db_Tip extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Model_Object_Tip',
        'Model_Collection_Interface' => 'Model_Collection_Tip',
        'Model_Db_Table_Interface'   => 'Model_Db_Table_Tip',
        'Model_Db_Table_Description' => 'Model_Db_Table_TipDescription',
        'Model_Mapper_Db_Plugin_Description',
    );

    public function init()
    {
        $this->addPlugin(
            'Description',
            $this ->getInjector()
                  ->getObject(
                    'Model_Mapper_Db_Plugin_Description',
                    array(
                        'mapper' => $this,
                        'table' => $this->getInjector()->getObject('Model_Db_Table_Description'),
                        'refColumn' => 'tip_id',
                        'descFields' => array(
                            'title', 'text',
                        ),
                    )
                  )
        )
        ;
    }


    /**
     * @param string
     * @param string
     * @return Model_Collection_Interface
     */
    public function fetchByDestinationAndRole($dest, $role)
    {
        $select = $this->fetchComplex(NULL, FALSE)
                       ->where('tip_destination = ?', $dest)
                       ->where('tip_role = \''.$role.'\' OR tip_role=\''.Model_Service::factory('role')->getViewAlias($role).'\'')
                       ->where('tip_status > 0')
                       /*->limit(1)*/
                       ;
        if ( ! $rows = $select->query()->fetchAll()) {
            $result = FALSE;
        }
        else {
            $result = $this->makeComplexCollection($rows);
        }
        return $result;
    }

    public function paginatorFetchAllActive($rowsPerPage, $page)
    {
        return $this->paginatorFetchComplex('tip_status > 0', $rowsPerPage, $page);
    }

    public function paginatorFetchAllArchive($rowsPerPage, $page)
    {
        $query = $this->fetchComplex(NULL, FALSE)->where('tip_status = 0')->order('tip_date_added DESC');
        return $this->paginator($query,  $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
    }

}
