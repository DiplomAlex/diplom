<?php

class Catalog_Model_Object_AttributeGroup extends Model_Object_Abstract
{

    public function init()
    {

        $this->addElements(array(
            'id',
            'status',
            'sort',
            'name',
            'brief',
            'rc_id', 'rc_id_filename', 'rc_id_preview',
            'date_added', 'date_changed',
            'adder_id', 'changer_id',
            'parent_id', 'tree_id', 'tree_level', 'tree_left', 'tree_right', 'children_count',
            'attribute_count',
        ));
    }


}