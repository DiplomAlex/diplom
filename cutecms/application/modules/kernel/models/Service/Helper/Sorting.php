<?php

class Model_Service_Helper_Sorting extends Model_Service_Helper_Abstract
{
    
    public function getSortingModes()
    {
        return $this->getService()->getMapper()->getPlugin('Sorting')->getSortingModes();
    }
    
    public function getCurrentSortingMode()
    {
        return $this->getService()->getMapper()->getPlugin('Sorting')->getCurrentSortingMode();
    }
    
    public function setCurrentSortingMode($modeName, $direction = NULL)
    {
        $this->getService()->getMapper()->getPlugin('Sorting')->setCurrentSortingMode($modeName, $direction);
        return $this;
    }
    
    public function getSortingDirection($modeName)
    {
        return $this->getService()->getMapper()->getPlugin('Sorting')->getSortingDirection($modeName);
    }
    
    public function setSortingModesOrder(array $modes)
    {
        $this->getService()->getMapper()->getPlugin('Sorting')->setSortingModesOrder($modes);
        return $this;
    }
    
    public function getSortingModesOrder()
    {
        return $this->getService()->getMapper()->getPlugin('Sorting')->getSortingModesOrder();
    }

}