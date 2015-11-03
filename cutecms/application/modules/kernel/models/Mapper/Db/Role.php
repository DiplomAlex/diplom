<?php

class Model_Mapper_Db_Role extends Model_Mapper_Db_Abstract implements Model_Mapper_Role
{

	protected $_defaultInjections = array(
		'Model_Db_Table_Interface' => 'Model_Db_Table_Roles',
		'Model_Object_Interface' => 'Model_Object_Role',
        'Model_Collection_Interface' => 'Model_Collection_Role',

        'Model_Db_Table_Role_Description' => 'Model_Db_Table_RolesDescription',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Sorting',
        'Model_Mapper_Db_Plugin_Resource',
        'Model_Db_Table_Resources',
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
                        'table' => $this->getInjector()->getObject('Model_Db_Table_Role_Description'),
                        'refColumn' => 'role_id',
                        'descFields' => array(
                            'name', 'brief',
                        ),
                    )
                  )
        )
        ->addPlugin('Resource',$this->getInjector()->getObject('Model_Mapper_Db_Plugin_Resource', array('rc_id')))
        ->addPlugin('Sorting', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Sorting'))
        ;
    }


    public function fetchOneByAclRole($aclRole)
    {
        try {
            $role = $this->fetchComplex(array('role_acl_role = ?'=>$aclRole))->current();
        }
        catch(Model_Exception $e) {
            $this->_throwException('role not found with acl_role = "'.$aclRole.'"');
        }
        return $role;
    }



}
