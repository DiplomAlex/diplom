<?php

class Model_Service_ArduinoLine extends Model_Service_Abstract
{
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_ArduinoLine',
        'Model_Mapper_Interface' => 'Model_Mapper_Db_ArduinoLine',
    );

    public function saveAndGetTime($sketchId)
    {
        $this->saveFromValues(array(
            'sketch_id' => $sketchId,
            'date_added' => date('Y-m-d H:i:s'),
        ));

        return $this->getTimeWithoutCurrent($sketchId);
    }

    public function getTimeWithoutCurrent($id)
    {
        $line = $this->getMapper()->fetchAllSortedWithoutCurrent($id);

        if ($line->count()) {
            $sec = ($line->count() * 60 * Model_Object_ArduinoLine::MINUTES_FOR_USER)
                - (time() - strtotime($line->current()->date_start));
        } else {
            $sec = 0;
        }

        return abs($sec);
    }

    public function deleteBySketchId($sketchId)
    {
        $this->getMapper()->deleteBySketchId($sketchId);
    }
}
