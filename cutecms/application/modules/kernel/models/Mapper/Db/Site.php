<?php

class Model_Mapper_Db_Site extends Model_Mapper_Db_Abstract
{

	protected $_defaultInjections = array(
		'Model_Db_Table_Interface' => 'Model_Db_Table_Sites',
		'Model_Object_Interface' => 'Model_Object_Site',
        'Model_Collection_Interface' => 'Model_Collection_Site',

		'Model_Mapper_Db_Vertical',
		'Model_Db_Table_Site_Description' => 'Model_Db_Table_SitesDescription',
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
						'table' => $this->getInjector()->getObject('Model_Db_Table_Site_Description'),
						'refColumn' => 'site_id',
						'descFields' => array(
							'title', 'brief', 'full',
							'html_title', 'meta_keywords', 'meta_description',
						),
					)
				  )
		);
	}



    /**
     * addons for complex select
     * @param Zend_Db_Select
     * @return Zend_Db_Select
     */
    protected function _onFetchComplex(Zend_Db_Select $select)
    {

        $select -> joinLeft(
                        'vertical',
                        'site_vertical_id = vertical_id',
                        array('site_vertical_skin' => 'vertical_skin')
                   );

        return $select;
    }

	/**
	 * @param string $host
	 * @param string $base
	 * @return Model_Object_Interface
	 */
	public function fetchOneByHost($host, $base = NULL)
	{
		if (empty($host)) {
			throw new Model_Mapper_Db_Exception('host should be set');
		}
                if (strtolower(substr($host, 0, 4))=='www.') {
                    $host = substr($host, 4);
                }
		$cond = array('site_host = ?' => $host);
		if ($base != NULL) {
                    $base = trim($base, '/');
                    if ( ! empty($base)) {
                        $cond['site_base_url = ?'] = $base;
                    }
		}
		else {
			$cond[] = 'isnull(site_base_url) OR site_base_url = \'\' OR site_base_url = \'/\' ';
		}
		if (( ! $rows = $this->fetchComplex($cond)) OR ($rows->isEmpty())) {
					$this->_throwException('it looks like record for site "'.$host.'" and base "'.$base.'" cannot be found in table "site" of database "'.App_PreBoot::getApplicationIniConfig()->resources->db->params->dbname.'"');
		}
		else {
                    $object = $rows->current();
		}
		return $object;
	}

	/**
	 * fetch all where id not in array
	 * @param array(int)
	 * @return array(Model_Object_Interface)
	 */
	public function fetchIdNotIn(array $idArray)
	{
		if (empty($idArray)) {
			throw new Mapper_Db_Exception('idArray is empty');
		}
		return $this->fetchComplex('NOT (site_id in ('.implode(',', $idArray).'))');
	}

	public function fetchByIdArray(array $ids)
	{
	    if (empty($ids)) {
	        $result = FALSE;
	    }
	    else {
	       $result = $this->fetchComplex(array('site_id IN (?)'=>$ids));
	    }
	    return $result;
	}

	public function fetchLinkedByDefault()
	{
	    $result = $this->fetchComplex(array('site_is_linked_by_default > 0'));
	    return $result;
	}

}

